---
title: Managing Roles
slug: managing-roles
order: 50
---

## Overview

The **Roles** page lets administrators review default roles, create custom roles, and configure which permissions each role grants within the current {{ entity_label }}.

## Opening the Roles page

1. Open your {{ entity_label }} menu.
2. Select **Roles** (requires permission to view or manage users for the current {{ entity_label }}).

{{ entity_label }} owners always have full access.

## What you can do

- Review **default roles** shipped with your application (for example President, Secretary, Owner).
- **Create custom roles** with descriptive names and selected permissions.
- **Copy** an existing role as a starting point for a new one.
- Drag roles to set **hierarchy** — higher roles may manage lower roles when your application supports it.
- Set **maximum members** on custom roles to limit how many people can hold the role.
- Enable **Directory: Council** on a role so those members appear in the council section of the [resident directory](/help/directory/member-directory).
- Choose a **badge colour** for how the role displays in member lists.

## Permission groups

Permissions are organized by area. When editing a role:

- **Bundle** permissions (shown in bold) grant a related set of abilities — toggling a bundle selects its child permissions.
- Individual permissions can be assigned without the full bundle when you need finer control.

Strata applications include groups such as **Help** (documentation and FAQ access), **Finances**, **Maintenance**, **Insurance**, and **Registered Charges**.

### Help permissions

| Permission | Purpose |
|------------|---------|
| **View Help & Playbook** | Open Help & Support documentation |
| **View Playbook FAQs** | Read published FAQ entries |
| **Manage Playbook FAQs** | Create, publish, reorder, and delete FAQ entries |

FAQ and documentation permissions are independent — assign each according to who should see guides vs. short FAQ answers.

## Assigning roles to members

Role assignment happens on the **Members** (or **Directory**) page, not on the Roles page itself. Open a member's record and change their assigned role. Changes take effect immediately.

## Related guides

- [Understanding roles](/help/platform/understanding-roles)
- [Members and invitations](/help/platform/members-and-invitations)
- [FAQ](/help/platform/faq)
