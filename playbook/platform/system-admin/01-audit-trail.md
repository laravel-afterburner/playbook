---
title: Audit Trail
slug: audit-trail
order: 10
system_admin: true
---

## Overview

The audit trail records significant actions across the application — who did what, when, and in which {{ entity_label }} context. It is available to **system administrators** only.

## Opening the audit trail

System administrators can open **Audit Logs** from the profile menu under System Administration.

## What is logged

Audit entries typically include:

- Authentication and security events
- Member and role changes
- **Impersonation** — user and role impersonation sessions, including who initiated them
- Feature-specific actions registered by packages (documents, voting, meetings, and others)

Exact coverage depends on which packages are installed and how auditing is configured.

## Searching and filtering

Use filters to narrow entries by user, {{ entity_label }}, event type, or date range. Detail views may show an **Impersonated by** field when an action occurred during user impersonation.

Export options may be available depending on configuration.

## Retention

Audit log retention is controlled at the application level. Consult your deployment documentation for storage duration and backup policies.

## Related guides

- [Impersonation](/help/platform/impersonation)
