---
title: API Tokens
slug: api-tokens
order: 20
feature: api
---

## Overview

When the **API** feature is enabled, you can create personal access tokens for programmatic access to your account. Tokens authenticate API requests the same way other Laravel Sanctum integrations do.

## Opening API tokens

Navigate to **API Tokens** at `/user/api-tokens` when the feature is enabled for your deployment. Some applications link this from the profile area; others require a direct URL.

## Creating a token

1. Enter a descriptive **token name** (for example `Mobile sync` or `CI deployment`).
2. Select **permissions** the token should grant, when your application exposes permission scopes.
3. Click **Create**.
4. Copy the token value immediately — it is shown only once.

Store tokens securely. Do not commit them to source control or share them in chat.

## Managing tokens

The token list shows existing tokens with their names and last-used dates. You can:

- **Edit permissions** on an existing token when supported.
- **Delete** tokens you no longer need.

Revoke compromised tokens immediately and create replacements.

## See also

- [Updating your profile](/help/platform/updating-your-profile)
