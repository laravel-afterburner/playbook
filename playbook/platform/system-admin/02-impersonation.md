---
title: Impersonation
slug: impersonation
order: 20
system_admin: true
---

## Overview

System administrators can troubleshoot permission and workflow issues by viewing the application as another user or as a specific **role**. Two modes are available from the profile menu under System Administration.

| Mode | Purpose |
|------|---------|
| **Impersonate User** | See the app exactly as a specific signed-in user |
| **Impersonate Role** | Experience permissions granted by a role within a {{ entity_label }} |

## Impersonate User

1. Open the profile menu as a system administrator.
2. Choose **Impersonate User**.
3. Search for and select the user to impersonate.

While impersonating, a banner indicates the active session. Perform only the actions necessary for support or verification.

Choose **Stop Impersonating** from the profile menu to return to your administrator account.

## Impersonate Role

Role impersonation helps verify what a role can see and do without changing a member's actual assignment.

1. Open the profile menu and choose **Impersonate Role**.
2. Select the {{ entity_label }} (when teams are enabled).
3. Search for and select the role to impersonate.

A banner shows which role is being impersonated. Choose **Stop impersonating role** from the profile menu when finished.

Role impersonation affects permission checks for your session; it does not modify member records or audit entries as if you were that user.

## Audit considerations

Impersonation sessions are recorded in the [audit trail](/help/platform/audit-trail). Entries may show **Impersonated by** with the administrator's name. Use impersonation sparingly and in accordance with your organization's support policies.

## Related guides

- [Audit trail](/help/platform/audit-trail)
