<img alt="tayssir logo" src="public/tayssir.svg">

---

This app provides a comprehensive platform
for
baccalaureate students to practice quizzes
and enhance their knowledge. With a user-friendly interface and a wide range of subjects to choose from,
students can test their understanding and track their progress. Join now and boost your exam preparation
with our interactive quizzes!The app provides a comprehensive platform for baccalaureate students to
practice quizzes and enhance their knowledge. With a user-friendly interface and a wide range of subjects to
choose from, students can test their understanding and track their progress. Join now and boost your exam
preparation with our interactive quizzes!

---

<!-- ## TODO

    - card translation !

    - material should have 2 colors
    - unit should have color attribute
    - chapter has image
    - each question should have a note (nullable)
    - account attributes

- -->

# changes :

-   phone number is unique (both in the database and in the request validation)
-   added refresh token system
-   access token expiration is set to 1 hour
-   refresh token expiration is set to 7 days
-   remove the device from auth request (not needed anymore after developing the refresh token system)
-   auth request (register and login) returns the access token and the refresh token
-   tokens now have two abilities (refresh_token, access_api)
-   to interact with any secured endpoint you should send the access token (bearer token)
-   to refresh the access token you should send the refresh token instead (bearer token)
-   return the otp in the response of the send verification email endpoint and request password change
