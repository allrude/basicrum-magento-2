# BasicRum Analytics — Improvement Plan

Cleanup + PHP 8.4 / Magento 2.4.8+ compatibility + beacon fix. Status of each item is tracked below.

## Decisions
- Target **PHP 8.3 / 8.4**, **Magento 2.4.8+**.
- Fix the beacon integration (boomerang was never `init`-ed) and add dynamic CSP for the beacon host.
- Remove the dead, non-functional consent UI.
- Ship a stable Boomerang build (not the "cutting-edge" one) with proper attribution.

## Checklist

### 1. Packaging & metadata — DONE
- [x] `composer.json`: PHP `~8.3.0 || ~8.4.0`; pinned real version ranges; added
      `magento/module-config`, `magento/module-backend`, `magento/module-csp`; dropped `*` wildcards,
      stale `archive.exclude`, and the module-level `repositories` block.
- [x] `LICENSE` added (MIT for module code + BSD-3-Clause third-party notice for Boomerang).
- [x] `README.md` corrected (PHP 8.3+/Magento 2.4.8+, working LICENSE link, CSP note).
- [x] `etc/module.xml`: `<sequence>` for `Magento_Store`, `Magento_Config`, `Magento_Backend`, `Magento_Csp`.

### 2. PHP cleanup & correctness — DONE
- [x] `Model/PageTypeDetector.php`: inject `Response\Http`; map `cms_noroute_index`; `=== 404`; memoize.
- [x] `Api/PageTypeDetectorInterface.php`: trimmed to `getPageType()`.
- [x] `ViewModel/Footer.php`: typed/null-safe `getBeaconEndpoint()`; scaffold comment removed; `readonly` promotion.
- [x] Admin blocks: return types added; `Logo` HTML moved to `view/adminhtml/templates/system/config/logo.phtml`
      + `css/basicrum-config.css` (via `adminhtml_system_config_edit.xml`); redundant ctor & unused imports removed.
- [x] PSR-12 fixed; `etc/di.xml` scaffold comment removed. (Note: `final` deliberately NOT added to
      DI-bound classes so Magento can still generate interceptors/plugins for them.)

### 3. Beacon integration — `view/frontend/templates/footer.phtml` — DONE
- [x] Boomerang is now initialized via `BOOMR_mq.push(["init", config])` (previously never init-ed → inert).
- [x] Deferred send implemented with `autorun: false` + a delayed `BOOMR.page_ready()` on window load
      (public API; replaces the never-registered custom `WaitAfterOnload` plugin).
- [x] Renders nothing when the endpoint is blank; standardized on `$escaper->escapeJs`; redundant
      view-model re-fetch removed. Kept CSP-safe `SecureHtmlRenderer`.

### 4. Dynamic CSP — `Model/Csp/BeaconPolicyCollector.php` + `etc/di.xml` — DONE
- [x] Collector adds the beacon origin (scheme+host+port) to `connect-src` + `img-src`; registered in
      `Magento\Csp\Model\CompositePolicyCollector`. Verified at runtime.

### 5. Remove dead consent config — DONE
- [x] `consent` group removed from `etc/adminhtml/system.xml`; `ConsentMode.php` deleted.

### 6. Boomerang asset — DONE (see note)
- [x] Renamed to version-agnostic `js/boomr/boomerang.min.js`; version centralized in `Model\Boomerang`.
- Note: investigation showed the file is **release build 815 with the "cutting-edge" *plugin flavor***
  (bundles Continuity), NOT an unstable nightly — "cutting-edge" is a plugin-set name. npm `latest`
  (1.815.1) is the same build. Per that finding we kept build 815 (same code, needed plugins) rather than
  rebuild. To change the bundle later, replace the file and update `Model\Boomerang::VERSION`.

## Verification — DONE
- [x] `composer validate` clean; `php -l` clean on PHP 8.4; all XML validated via `setup:upgrade`.
- [x] `module:enable` + `setup:upgrade` + `cache:flush` succeeded.
- [x] Runtime (Magento-bootstrapped): interface preference resolves; ViewModel null-safe; CSP collector
      emits correct policies and is registered in the composite collector.
- [ ] TODO (needs a browser + configured endpoint): confirm the beacon actually fires with `p_type` +
      `p_gen=mage2` and that there are no CSP violations for the beacon host.
