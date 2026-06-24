# Privacy Policy

This document outlines the data handling practices of the Community Weather Forecast Dashboard. As a self-hosted application designed in alignment with Digital Public Good principles, we prioritize transparency and user control.

---

## Data You Control

Because this is a **self-hosted application**, all data collected remains on the server you install it on. You have full control and responsibility for this data. We, the developers of the application, do not have access to your installation or any information your users submit.

## Data Collected

The application collects two main types of data:

### 1. User Feedback Data

To improve the service and validate forecast accuracy, the application includes a voluntary feedback form. When a user submits feedback, we collect the following information:

*   **User Location (Entered):** The location the user provides (e.g., "My Farm, Barangay 1"). This is essential for contextualizing the feedback and understanding forecast accuracy in specific areas.
*   **Outdoor Activity Information:** Whether the app was used for an outdoor activity, the type of activity (e.g., drying crops, laundry), and any other details provided. This helps us understand the app's real-world use cases.
*   **Forecast Ratings:** The user's rating of rainfall accuracy and whether the forecast was helpful. This is direct, qualitative data on model performance.
*   **Model Preference:** Which weather model the user found more accurate.

This feedback data is stored in the `feedback` table in your database. It is only accessible to logged-in administrators.

### 2. Staff and Admin Credentials

The application uses a role-based access control system. The following information is stored in the `users` table:

*   **Username:** Used for logging in.
*   **Hashed Password:** Passwords are never stored in plaintext. They are salted and hashed for security.
*   **Role:** The user's role (admin or staff).

## Data Not Collected

The application **does not**:

*   Use cookies or IP Address for tracking users across other sites.
*   Collect any personal information beyond what is explicitly asked for in the user management and feedback forms.
*   Transmit any of your data to the application developers or any other third party.

## Data Sharing and Third Parties

The application communicates with two third-party weather services:

*   MET Norway
*   Pirate Weather

When fetching a forecast, it sends the requested coordinates (latitude and longitude) and the User-Agent specified in your configuration. It **does not** send any user-identifying information to these services.

## Your Responsibilities as an Operator

As the operator of your own instance of this application, you are the data controller. It is your responsibility to secure your server, manage user access, and comply with any local data privacy regulations.