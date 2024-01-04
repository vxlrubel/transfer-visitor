# Transfer Visitor WordPress Plugin

The "Transfer Visitor" WordPress plugin facilitates URL redirection management via the WordPress dashboard using REST API endpoints. This plugin enables users to set up redirection pairs from old URLs to new destinations efficiently.

## Installation

1. Upload the `transfer-visitor` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the WordPress 'Plugins' menu.

## Features

- Add and manage redirection pairs of old URLs to new destinations.
- Utilizes REST API endpoints for easy interaction and management.
- Allows modification and deletion of existing redirection pairs.
- Provides a streamlined interface within the WordPress admin panel.

## Usage

Once activated, navigate to the plugin's settings page in the WordPress admin panel. Here, users can:

- Add new redirection pairs by specifying old URLs and their corresponding new destinations.
- View, edit, or delete existing redirection pairs.
- Choose to keep or delete associated database table upon deactivation.

## REST API Endpoints

The plugin offers the following REST API endpoints:

- `GET /wp-json/transfer-visitor/v1/redirections`: Retrieve all redirection pairs.
- `POST /wp-json/transfer-visitor/v1/redirections`: Add a new redirection pair.
- `GET /wp-json/transfer-visitor/v1/redirections/{id}`: Retrieve a specific redirection pair.
- `PUT /wp-json/transfer-visitor/v1/redirections/{id}`: Update an existing redirection pair.
- `DELETE /wp-json/transfer-visitor/v1/redirections/{id}`: Delete a redirection pair.

## Contributions

Contributions are welcome! To contribute to this plugin, please follow the guidelines outlined in [CONTRIBUTING.md](link-to-contributing-file).

## Issues

For any issues, bugs, or feature requests, please create an issue on the [Issues](link-to-issues) page.

## License

This project is licensed under the GNU General Public License v2.0 - see the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) file for details.

## Changelog

### 1.0
- Initial release of the "Transfer Visitor" plugin.

## Author

- Rubel Mahmud ( Sujan )
- Twitter: [Linkedin](https://www.linkedin.com/in/vxlrubel/)
- Twitter: [Reddit](https://www.reddit.com/user/vxlrubel)
- Twitter: [Twitter](https://twitter.com/vxlrubel)
- Website: [Facebook](https://www.facebook.com/rubel.ft.me)
- Twitter: [Instagram](https://www.instagram.com/vxlrubel/)
