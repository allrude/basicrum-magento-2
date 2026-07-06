# Changelog

All notable changes to the BasicRum Analytics module are documented here.
This project adheres to [Semantic Versioning](https://semver.org/).

## [0.1.0]

A compatibility, correctness, security and cleanup release. Two of the changes fix real functional
bugs (beacons were not actually initialised; beacons were blocked under strict CSP). The public
configuration path (`basicrum/general/*`) is unchanged, so existing configuration keeps working.

### Fixed (functional bugs)

- **Beacons are now actually initialised.** The frontend template built a `basicRumBoomerangConfig`
  object (beacon URL, cookie flags, ResourceTiming/Continuity settings) but **never passed it to
  Boomerang**, so those settings were inert. The bootstrap now initialises Boomerang through its method
  queue: `BOOMR_mq.push(["init", config])`. _Why:_ without an `init` call Boomerang never learns the
  beacon URL, so beacons could not be delivered as configured.
- **Deferred send reimplemented with the public API.** The old code defined a custom
  `BOOMR.plugins.WaitAfterOnload` object that was never registered in Boomerang's plugin list, so it did
  nothing. The "send a fixed delay after the page loads" behaviour is now implemented with the supported
  `autorun: false` config plus a delayed `BOOMR.page_ready()` call on window `load`. _Why:_ custom
  plugins must be compiled into the Boomerang build to run; the public `page_ready()` call achieves the
  same intent reliably.
- **Content Security Policy: beacon host is whitelisted automatically.** Added
  `Model/Csp/BeaconPolicyCollector` (a `Magento\Csp\Api\PolicyCollectorInterface`) that reads the
  configured Beacon Endpoint and adds its origin to the `connect-src` and `img-src` fetch policies.
  _Why:_ Magento 2.4+ ships strict CSP. Boomerang delivers beacons via XHR / `navigator.sendBeacon`
  (`connect-src`) and image beacons (`img-src`); on a CSP-enforcing store those requests were blocked.
  Because the endpoint is admin-configurable it cannot be a static `csp_whitelist.xml` entry, so the
  host is resolved at runtime.

### Added

- **Beacon Token / Site Key field** (`basicrum/general/token`). Optional, stored **encrypted**
  (`obscure` field + `Encrypted` backend model). When set, the ViewModel decrypts it and appends it to the
  beacon URL automatically as `&token=…` (URL-encoded, respecting any existing query string). Leave blank
  for collectors that need no token. The CSP whitelist is unaffected (the token lives in the query string,
  not the host).

### Changed (compatibility)

- **PHP 8.3 / 8.4 support.** `composer.json` PHP constraint changed from `^8.1|^8.2|^8.3`
  (which *excluded* 8.4) to `~8.3.0 || ~8.4.0`. _Why:_ Magento 2.4.8 requires PHP 8.3 and 2.4.9 adds
  8.4; the old constraint blocked installation on supported platforms.
- **Real dependency constraints.** Replaced the `magento/framework: *` / `magento/module-store: *`
  wildcards with bounded ranges, and added the modules actually used at runtime:
  `magento/module-config`, `magento/module-backend`, `magento/module-csp`.
- **Module load order.** `etc/module.xml` now declares a `<sequence>` for `Magento_Store`,
  `Magento_Config`, `Magento_Backend` and `Magento_Csp`. _Why:_ the module relies on these at runtime;
  declaring them guarantees correct load/upgrade order.
- **`PageTypeDetector` uses the correct response type.** It injected the generic
  `App\ResponseInterface` and then called `getStatusCode()`, which that interface does not declare. It
  now injects `App\Response\Http`, uses a strict `=== 404` comparison, and maps `cms_noroute_index` to
  the 404 page type. The result is memoised.

### Changed (code quality & security)

- `ViewModel/Footer` exposes a typed, null-safe `getBeaconEndpoint(): string` (returns `''` when unset)
  and the config path constant `XML_PATH_BEACON_ENDPOINT`, replacing the loosely-typed `getConfig()`
  array that could leak `null` into the template.
- The frontend template renders nothing when no endpoint is configured, standardises on the injected
  `$escaper->escapeJs()`, drops a redundant view-model re-fetch, and keeps the CSP-safe
  `SecureHtmlRenderer` script rendering.
- `Api\PageTypeDetectorInterface` trimmed to the single method in use (`getPageType()`); the unused
  `isHomePage()/isProductPage()/isCheckoutPage()` methods were removed.
- Admin config field blocks gained return types. The `Logo` field's markup moved out of PHP into
  `view/adminhtml/templates/system/config/logo.phtml` + `view/adminhtml/web/css/basicrum-config.css`
  (loaded via `adminhtml_system_config_edit.xml`), removing inline styles and a redundant constructor.
- The Boomerang version string is centralised in `Model\Boomerang` (single source of truth) instead of
  being duplicated across the JS filename and the admin display block.
- Beacon Endpoint field now validates as a URL (`validate-url`) and documents the CSP behaviour in its
  admin comment.
- Added a real `LICENSE` (MIT for the module) with a **BSD-3-Clause third-party notice** for the bundled
  Boomerang library, and corrected the README (it previously claimed PHP 7.2 / Magento 2.3).

### Removed

- **Non-functional consent UI.** The `consent` configuration group and its `ConsentMode` source model
  were removed. _Why:_ the fields were exposed in the admin but never read by any code, so they gave a
  false impression that consent gating was in effect. (Consent gating can be reintroduced as a real
  feature later.)

### Notes

- The vendored Boomerang file was renamed from `boomerang-1.815.60.cutting-edge.min.js` to the
  version-agnostic `boomerang.min.js`. Investigation confirmed this is **release build 815 with the
  "cutting-edge" plugin flavor** (which bundles the Continuity plugin) — "cutting-edge" is a
  *plugin-set name*, not an unstable channel; npm `latest` (1.815.1) is the same build. The code was
  kept as-is; only the filename and version bookkeeping changed.
- To upgrade the bundled library later, replace `view/frontend/web/js/boomr/boomerang.min.js` and update
  `Model\Boomerang::VERSION`. No template change is required (the path is version-agnostic).

## [0.0.2] — previous

- Initial fork baseline: ViewModel-based rendering, PHP 8 constructor property promotion, removed
  setup version, Boomerang build without debug logging.
