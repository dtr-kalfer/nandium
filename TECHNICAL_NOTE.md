# Nandium Weather Dashboard: A Community-Centric Weather Dashboard for Agricultural Planning

**Abstract:**
Access to reliable, localized weather forecasts is critical for climate-resilient agriculture. Smallholder farmers, particularly in regions with variable microclimates, require actionable data to mitigate risks associated with post-harvest activities, such as crop drying. This technical note describes a lightweight, open-source PHP web application designed to address this need. The dashboard provides a comparative 24-hour forecast by aggregating data from two distinct meteorological models: the Norwegian Meteorological Institute (MET Norway) and Pirate Weather (a Dark Sky API-compatible service). By offering a simple, self-hostable platform, the tool empowers local communities and agricultural extension officers to make more informed, data-driven decisions. The system includes features for collecting user feedback on forecast accuracy, creating an automated data-archiving pipeline for long-term analysis, and role-based access for custom location forecasting. This project is aligned with the principles of a Digital Public Good (DPG), promoting open data, sustainability, and community empowerment.

![Nandium App](./readme_assets/nandium_app_show.avif)

---

**1. Introduction**

In many agricultural communities, the period following a harvest is fraught with risk. Unpredicted rainfall can ruin crops, such as *palay* (unhusked rice), that are left to dry outdoors. While national weather services provide regional forecasts, localized weather phenomena often go uncaptured. This project was initiated to provide a simple, accessible tool for communities in areas like Burauen, Leyte, Philippines, to get a more granular, short-term view of probable rainfall.

The primary objective is to create a low-maintenance, easy-to-deploy web application that:
-   Presents a 24-hour forecast from multiple reputable sources.
-   Allows for easy configuration to any geographic location.
-   Enables data collection for both real-time decision-making and long-term climate monitoring.

**2. System Architecture & Technology Stack**

The application is built using a standard, widely accessible technology stack to ensure ease of deployment and maintenance, even in low-resource environments.

-   **Backend:** PHP (procedural and object-oriented practices)
-   **Database:** MySQL
-   **Frontend:** HTML, CSS, vanilla JavaScript

Key components include:

-   **Configuration Module (`new_records.php`):** A web-based interface for administrators to set system-wide parameters, including default coordinates, API keys, and cache settings.
-   **Weather Data Handlers (`metweather/`, `pirateweather/`):** PHP scripts responsible for fetching, caching, and displaying data from the respective APIs.
-   **Role-Based Access Control (RBAC):** A user management system with 'Admin' and 'Staff' roles, allowing for secure configuration and specialized use cases (e.g., field officers adding temporary locations).
-   **Feedback System (`feedback.php`, `list_feedback.php`):** A mechanism for end-users to submit qualitative feedback on forecast accuracy, which is then viewable and exportable by administrators.
-   **Archiving API (`met_api.php`, `pirate_api.php`):** Cron job-compatible scripts that create daily dumps of forecast data in both JSON and CSV formats, building a historical dataset over time.

**3. Data Sources & Integration**

The system leverages two distinct weather models to provide a more robust forecast:

-   **MET Norway API:** Utilizes the `locationforecast` endpoint, which provides high-resolution, global weather data. This source is valued for its comprehensive modeling.
-   **Pirate Weather API:** A free alternative that maintains compatibility with the once-popular Dark Sky API. It is particularly useful for its detailed precipitation intensity and probability forecasts.

To manage API call efficiency and respect service limits, the application implements a server-side caching mechanism for all API requests.

**4. Conclusion & Future Work**

This Community Weather Forecast Dashboard serves as a practical tool for climate adaptation at the local level. By making weather data more accessible and context-specific, it directly supports agricultural planning and community resilience. The automated archiving feature paves the way for future work in analyzing microclimate trends and validating model performance over the long term.

Future development could include:
-   Integration of more local or regional weather models.
-   Automated analysis and visualization of the archived feedback and forecast data.
-   SMS or email-based weather alerts for registered users.

As an open-source project, we welcome contributions that extend its functionality and adapt it to the needs of other communities worldwide.