# playground
URL shortener example

## Challenge Overview

Design and implement a URL shortener service with an optional separate
analytics service.  You can choose to use either SQLite for data
persistence or an in-memory data structure.  The service should be
deployable locally, with the option to deploy on Google Cloud Run or
Knative.

## URL Shortener Service Requirements

These are the basic requirements. You don't need to implement anything
beyond these main requirements if you don't want to.

1. The service should accept a long URL and return a unique shortened URL.
2. When a user accesses the shortened URL, the service should redirect them to the
original long URL.
3. The service should have a basic API for creating and retrieving URLs.
4. The service should handle edge cases, such as invalid or duplicate URLs.
5. The service should be scalable and efficient.
6. The service should be deployable locally, either by running it from the command line or
using a Dockerfile.
