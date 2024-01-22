# Havoc-DRM-Middleman
Havoc-DRM-Middleman is a PoC API designed to communicate with the Havoc DRM API using the [Model-View-Controller](https://programmingdive.com/understanding-mvc-design-pattern-in-php/) design-pattern.

This project is written in PHP and containerized using [Docker](https://docs.docker.com/get-docker/) for easy deployment.

The client-side code can be found here: https://github.com/yandevelop/Havoc-DRM-Client

## Description
The project is intentionally kept simple with minimal overhead.

It serves as an example for making API calls to the Havoc DRM API to verify ownership of a package. 
Using a middleman in helps to keep your authentication token secure.

## Running
This middleman has only been tested in a local environment.

1. Clone this repository using
`git clone https://github.com/yandevelop/Havoc-DRM-Middleman`
2. cd into the directory
`cd Havoc-DRM-Middleman`
3. Run
`docker-compose up`

After running the above command, the web server will be accessible from `localhost:80`.

## Usage
1. Generate a authentication token for your package from your Havoc Settings.
Replace the authentication token in .env with the appropriate one for your package, following this scheme:<br>
 `PACKAGEIDENTIFIER.TOKEN=YOUR_TOKEN` <br>
Example: <br>
`COM.YAN.STELLA.TOKEN=ieK4WwV1K5PNd9ZFSXEmCH7NGV4a6GQZkx0Q3TRZ`<br>

Multiple tokens for various packages can be managed using this scheme.

**Note:**
You could also use a seller-based token; however, you'd need to edit the API class to not look up the token based on the package identifier.

2. From your client, make a POST request to https://ipaddress:80/api/verify with the following JSON data and ensure that the request header has `Content-Type: application/json`, otherwise the server will reject your request:
```JSON
{
  "udid": "DEVICE-UDID",
  "model": "DEVICE-MODEL",
  "identifier": "PACKAGE-IDENTIFIER"
}
```

A proper request to the middleman could look something like this:
```JSON
{
  "udid": "00008020-008D4548007B4F26",
  "model": "iPhone14,2",
  "identifier": "com.your.tweak"
}
```

The middleman validates if any parameters are missing and checks if an authorization token exists in the .env. If any of these checks fail, the middleman responds with an error and exits.

A request to the Havoc API is only made if all parameters are non-empty and an authentication token exists in the .env for the given identifier.

If the request to the Havoc API succeeds, the response from the Havoc API will be sent directly to the client.

## Recommendation
- Add a hashed secret to your response if the device is confirmed to own the package. 
- You can also implement rate limiting and request logging to the middleman.

## Documentation
This PoC was created using the following resources:

- Havoc DRM API Documentation (Havoc) - https://docs.havoc.app/docs/seller/drm/
- Tweak DRM (iPhone Development Wiki)- https://iphonedev.wiki/Tweak_DRM
- Packix-DRM-Middleman (guillermo-moran) - https://github.com/guillermo-moran/Packix-DRM-Middleman/