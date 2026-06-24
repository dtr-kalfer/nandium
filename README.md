# Nandium Weather Dashboard

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A lightweight, self-hostable PHP web application designed to provide localized, short-term weather forecasts. This tool helps communities, especially in the agricultural sector, make informed decisions by comparing data from multiple reliable weather sources.

This project is designed with the principles of a **Digital Public Good (DPG)** in mind: it is open-source, relevant to sustainable development goals, and includes safeguards for privacy.

![nandium sample|697](./readme_assets/nandium_app.avif)

![nandium app show](./readme_assets/nandium_app_show.avif)


---

## ⚠️ Important Disclaimer

**This application is intended for informational and planning purposes only.** The weather data provided is retrieved from third-party sources and is not a substitute for official meteorological bulletins.

**DO NOT use this application for making critical, life-threatening decisions.** This includes, but is not limited to, maritime navigation, aviation, extreme sports, or any activity where personal safety is a factor. Always consult and verify with your national weather service or other official sources.

---

## Alignment with UN Sustainable Development Goals (SDGs)

This project contributes to the following SDGs:

*   **SDG 2: Zero Hunger:** By providing farmers with rainfall forecasts, it helps them plan post-harvest activities like drying crops (`palay`, etc.), reducing waste and improving food security.
*   **SDG 11: Sustainable Cities and Communities:** Empowers local communities with actionable data for daily planning (events, construction, etc.) and raises awareness of weather patterns.
*   **SDG 13: Climate Action:** Facilitates the collection of localized weather data, which can be used for micro-climate analysis and building community resilience.

---

## Features

*   **Dual Weather Sources:** Compares 24-hour forecasts from MET Norway and Pirate Weather.
*   **Unified Configuration:** Easy setup via a web form for location, API keys, and other settings.
*   **Role-Based Access Control (RBAC):**
    *   **Admin Role:** Manages system configuration and user credentials.
    *   **Staff Role:** Can add and manage custom locations for on-demand forecasts, ideal for field officers.
*   **User Feedback System:** Collects user feedback on forecast accuracy to help validate and improve the models.
*   **Automated Data Archiving:** Includes cron job-ready API scripts to automatically save daily forecasts as JSON and CSV files for historical analysis.
*   **Privacy-Conscious:** Designed to be self-hosted, giving you full control over your data. See `PRIVACY.md` for details.

---

## API Usage Policies & Limits

This application relies on external APIs. You are responsible for adhering to their terms of service.

*   **MET Norway:**
    *   **Policy:** Data is provided for free under a Creative Commons 4.0 license.
    *   **Requirement:** You **must** provide a valid `User-Agent` in all requests that identifies your application. Our configuration page requires this.
    *   **Limits:** There are no hard rate limits, but caching is strongly encouraged to avoid overloading their servers. This app implements a default 30-minute cache.

*   **Pirate Weather:**
    *   **Policy:** A free, Dark Sky-compatible forecast API.
    *   **Requirement:** Requires a free API key.
    *   **Limits:** The free tier has a limit on the number of API calls you can make per day. The built-in caching helps stay within these limits.

---

## Installation

1.  **Prerequisites:** A web server with PHP and a MySQL database (e.g., WAMP, LAMP, XAMPP).
2.  **Clone Repository:** Download or clone this repository to your web server's public directory.
3.  **Database Setup:**
    *   Create a new MySQL database (e.g., `weather_feedback`).
    *   Create a database user with privileges for that database (e.g., `weather_feedback_user`).
    *   Update `weather_app/dbParams.php` with your database credentials.
    *   Import the `weather_app/schema.sql` file into your database to create the necessary tables.
4.  **Initial Admin Setup:**
    *   Open your web browser and navigate to `weather_app/new_admin.php`.
    *   Create your primary administrator account.
    *   **For security, delete the `new_admin.php` file immediately after use.**
5.  **Application Configuration:**
    *   Navigate to `weather_app/login.php` and log in as the administrator.
    *   You will be redirected to the setup page (`new_records.php`). Fill in your default location, API keys, and other settings.

---

## License

This project is licensed under the **MIT License**. See the `LICENSE` file for more details.