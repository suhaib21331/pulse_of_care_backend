# Plan: Email Verification Code using SMTP

## Goal

Implement email verification using SMTP. When a user registers, the backend should generate a verification code, send it to the user's email, store the code securely, and verify it before allowing the user to complete authentication/profile flow.

---

## Current Project Context

- Laravel backend.
- Authentication logic is organized using services.
- Existing auth-related logic should stay inside Auth-related folders/services.
- Do not put verification logic directly inside controllers.
- Controllers should stay thin.
- Business logic should go into services.

Recommended structure:

```txt
app/
  Http/
    Controllers/
      api/v1/
        AuthController.php

    Requests/
      VerifyEmailCodeRequest.php
      ResendEmailCodeRequest.php

  Services/
    Auth/
      EmailVerificationService.php

## TODO: Change Email Verification Compatibility

- Pass the target email address into `SendEmailVerificationJob` instead of relying on a rehydrated `User` email when queued mail runs later.
- Normalize emails to lowercase and consider allowing the current user's existing email if unchanged email submissions should be accepted.
- Hide `email_verification_code` and usually `email_verification_expires_at` from serialized `User` API responses.