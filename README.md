# BasicRum Analytics for Magento 2

BasicRum Analytics is a Magento 2 extension that helps you collect and analyze real user monitoring (RUM) data for your Magento store, providing insights into your website's performance from the user's perspective.

It injects the [Boomerang](https://github.com/akamai/boomerang) RUM library on the storefront, tags each beacon with the current page type (`home`, `product`, `category`, `checkout`, …) and `p_gen=mage2`, and sends performance beacons to a configurable BasicRUM collector endpoint.

## Requirements

- Magento Open Source or Commerce **2.4.8** or higher
- PHP **8.3** or **8.4**

## Installation

```sh
composer require basicrum/basicrum-analytics
bin/magento module:enable BasicRum_Analytics
bin/magento setup:upgrade
bin/magento cache:flush
```

## Configuration

1. Log in to your Magento Admin Panel
2. Navigate to **Stores > Configuration > BasicRum > BasicRum Analytics**
3. Configure the following options:
   - **Enable**: Set to "Yes" to enable the extension
   - **Beacon Endpoint**: The URL where beacons are sent. This is the endpoint where a BasicRUM beacon catcher is running.

4. Click "Save Config" to apply the changes
5. Flush the cache (**System > Cache Management > Flush Magento Cache**)

### Content Security Policy

The module registers a dynamic CSP policy collector that automatically whitelists the configured
**Beacon Endpoint** host under `connect-src` and `img-src`. No manual `csp_whitelist.xml` editing is
required when you change the endpoint — the host is derived from the configured value at runtime.

## Documentation

- **[User Guide](docs/user-guide.html)** — a complete, self-contained setup guide (open in a browser):
  installation, getting your beacon endpoint/token, configuration, CSP, verification, FAQ and troubleshooting.
- **[CHANGELOG](CHANGELOG.md)** — what changed and why in each release.

## Verification

To verify that the extension is working properly:

1. Open your store in a web browser
2. Open the browser's developer tools (F12)
3. Check the Network tab for beacon requests to the configured Beacon Endpoint (they carry `p_type` and `p_gen=mage2`)
4. Confirm there are no CSP violations in the Console for the beacon host
5. Visit your BasicRum dashboard to confirm that data is being collected

## License

This extension's own code is released under the [MIT License](LICENSE). The bundled Boomerang library is
distributed under the BSD 3-Clause License — see the third-party notice in [LICENSE](LICENSE).
