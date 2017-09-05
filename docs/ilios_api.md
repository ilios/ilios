# Ilios API Documentation

This document provides instructions for connecting to the Ilios API for making requests to an Ilios backend from third-party applications.

## API Endpoints
The functional endpoints provided by the Ilios API, and how they are called, are listed and documented at /api/doc on the webserver that runs Ilios. For example, to examine the API endpoints and their use on the publicly-accessible Ilios demo server ([https://ilios3-demo.ucsf.edu](https://ilios3-demo.ucsf.edu)), one would just need to visit https://ilios3-demo.ucsf.edu/api/doc in their browser.

## API Authentication - Using JSON Web Tokens

To make calls to any of the endpoints provided by the Ilios API, a valid JSON Web Token (JWT) must be sent along in the headers of the HTTP request.  JWT's can be created and invalidated as-needed by any user when they visit their profile page (/myprofile) in the Ilios application. The permissions for the token reflect those of the account of the user creating the token - any tasks available to the user within the Ilios frontend GUI application will be available to the user's API request(s) when making API calls using the JWT token they generated.

### Creating a JSON Web Token (JWT)

To create a new JWT, a user should log into their Ilios application and visit their profile page at /myprofile (eg, [https://ilios3-demo.ucsf.edu/myprofile](https://ilios3-demo.ucsf.edu/myprofile)). Once on their profile page, they can create a new token by clicking on the 'Create New' button, selecting an expiration date for the token, and copying the JWT text returned. For security reasons, it is ill-advised to create a token with an expiration date far out into the future; tokens should only remain valid for their intended duration and no longer.  All tokens no longer being used should be invalidated ASAP.

### Creating JWT tokens from the command line

JSON Web Tokens can also be created by an Ilios administrator using the Ilios console application at the command-line and running the following command in the context of the user that runs the webserver (eg, 'apache').  The Ilios user_id = 18 for this example:

```bash
sudo -u apache bin/console ilios:maintenance:create-user-token 18 --env=prod
```

### Authenticating requests using 'X-JWT-Authorization' in your HTTP request headers

The value of JWT that was copied when the token was generated should be added to the HTTP headers as the value associated with 'X-JWT-Authorization' header and it should be prefixed with the work 'Token ' (the word 'Token', followed by a single space).  For example, if the token generated is:

```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJpbGlvcyIsImF1ZCI6ImlsaW9zIiwiaWF0IjoiMTQ3OTE2NDIxNSIsImV4cCI6IjE0ODA0MDY0MDAiLCJ1c2VyX2lkIjoxNn0.45RN1Tw9bd_dgeiGVTJCm8sy_x4UD_a9xE4hHYS6H08
```

then the header attribute sent should look like this:

```
X-JWT-Authorization: Token eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJpbGlvcyIsImF1ZCI6ImlsaW9zIiwiaWF0IjoiMTQ3OTE2NDIxNSIsImV4cCI6IjE0ODA0MDY0MDAiLCJ1c2VyX2lkIjoxNn0.45RN1Tw9bd_dgeiGVTJCm8sy_x4UD_a9xE4hHYS6H08
```
(Note the space between the word 'Token' and the token value itself!)

This is the header value that should be sent with every HTTP request that uses this token to authenticate.

### Verifying/testing JWT Authentication

There are two ways to easily test the validity of a JSON Web Token, through the Ilios API Sandbox (at /api/doc on your Ilios installation) or by using a browser extension for either Google Chrome or Firefox.  Both methods are described below:

#### Verifying JWT in the Ilios API Sandbox (at /api/doc)

Aside from being a great source of up-to-date documentation of the methods available via the Ilios API, the Ilios API endpoint reference also provides a sandbox for testing specific calls using your JWT.  To test your newly created JWT in the API sandbox, do the following:

1. Visit /api/doc on your Ilios installation (for this example, we'll use [https://ilios3-demo.ucsf.edu/api/doc](https://ilios3-demo.ucsf.edu/api/doc))
2. Scroll down and find the Ilios API endpoint that you would like to test (ie, 'User')
3. Expand the desired endpoint's documentation by clicking on the request method (ie, "GET")
4. In the area that expands, you will see the endpoint's documentation with two options listed at the very top: 'Documentation' and 'Sandbox'.
5. Click on the 'Sandbox' link and you will be presented with several form fields. Find the ones under the 'Headers' label.  There should be one field for 'Key' and another for 'Value'.
6. In the 'Key' field, enter this text: X-JWT-Authorization
7. In the 'Value' field, enter 'Token [YOUR JWT VALUE]' (Replacing [YOUR JWT VALUE] with the value of the token you are testing) as shown:
```
Token eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJpbGlvcyIsImF1ZCI6ImlsaW9zIiwiaWF0IjoiMTQ3OTE2NDIxNSIsImV4cCI6IjE0ODA0MDY0MDAiLCJ1c2VyX2lkIjoxNn0.45RN1Tw9bd_dgeiGVTJCm8sy_x4UD_a9xE4hHYS6H08
```

Now, when you click the 'Try!' button at the bottom of that section, you should see the JSON-formatted result of the API call! Congratulations! Your JWT token is working correctly!

#### Verifying JWT using browser extensions for Chrome or Firefox

If you would like to test the functionality of your new JWT and verify that it is working and/or that the proper results are being returned as-expected WITHOUT using the Ilios API Sandbox, we recommend you test your JWT using the browser extension [Modify Headers for Google Chrome](https://chrome.google.com/webstore/detail/modify-headers-for-google/innpjfdalfhpcoinfnehdnbkglpmogdi) or the [Modify Headers extension for Firefox](https://addons.mozilla.org/en-US/firefox/addon/modify-headers/).  These extensions will allow you to test a token and view the results of API calls directly within the browser.

Using the 'Modify Headers for Google Chrome' extension in Google Chrome as an example, visit the extension's configuration page and do the following:

1. Click the 'Add New' button - a new row should appear to allow for adding a new header.
2. In the new row that appears, select 'Add' for the 'Action' attribute and enter 'X-JWT-Authorization' for the 'Name' attribute.
3. Under 'Value', enter the following exactly as displayed below and save/enable it when you're done:

```
Token eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJpbGlvcyIsImF1ZCI6ImlsaW9zIiwiaWF0IjoiMTQ3OTE2NDIxNSIsImV4cCI6IjE0ODA0MDY0MDAiLCJ1c2VyX2lkIjoxNn0.45RN1Tw9bd_dgeiGVTJCm8sy_x4UD_a9xE4hHYS6H08
```

If all went as planned, this header value should now be sent with EVERY request you make from the browser until you choose to disable it or you disable and/or uninstall the extension entirely.

To test that the header is being sent correctly, visit your Ilios API instance and check for users by appending '/api/v1/users' to your own Ilios URL as shown here:

https://ilios3-demo.ucsf.edu/api/v1/users

If adding the token to the headers was successful, you will see a JSON-formatted display of all the user accounts in Ilios. Congratulations! Your JWT token is working correctly!

The steps for using the Firefox extension are VERY similar to the ones listed above for Google Chrome and should be fairly simple to figure out.

NOTE: when you are finished testing your JWT, remember to disable the added header or disable the extension entirely! If you leave it enabled, attempting to sign into the Ilios GUI from this browser will return an error and prevent you from logging in!
