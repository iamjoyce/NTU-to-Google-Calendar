# NTU-to-Google-Calendar
Automatically generates NTU timetable in Google Calendar

Completed, but tiny details are not settled.

------------------------------------
### Completed
- Saves similar class types together (e.g. Tutorials into one calendar, Lectures into another)
- Allows calendar names to be different from defaults
- Allows creation of a calendar for week numbers only
- Allows different dates to be selected
- Removes classes in recess week
- Retrieves details about the location from [NTU map](http://maps.ntu.edu.sg/maps)

------------------------------------
### Configuration
- ./assets/classes/GoogleAuth.class.php
    - AUTHCONFIGFILE: client_secret.json
    - REDIRECT_DEV: where to redirect the user after authorisation attempt
    - Refer to [Google Calendar API Documentation](https://developers.google.com/api-client-library/php/auth/web-app) for details
- ./oauth2callback.php
    - REDIRECT_AFTER_AUTH: where to redirect the user after *successful* authorisation

------------------------------------
### Todo
- Comments
- Odd and even week classes after recess week is incorrect
    - You must manually adjust the dates after recess week for odd and even week classes