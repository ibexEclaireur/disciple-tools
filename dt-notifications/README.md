# DT-Notifications
The Notifications module contains the utilities necessary for web notifications, email notifications, and alerts between workers.

## Core Files

1. `/hooks/`   
   _The Hooks folder contains the hook factory files that catch the appropriate activities and logs them to the notifications table._
1. `notifications.php`  
   _description_
1. `notifications-email-api.php`  
   _description_
1. `notifications-endpoints.php`   
   _description_
1. `notifications-hooks.php`  
   _description_
1. `notifications-template.php`  
   _description_

## DB Table Configuration

The primary notifications table is `$wpdb->dt_notifications`, i.e. `wp_dt_notifications` on default installations. The 
columns and their purposes are listed below.

### @mentions

| Column Name           | Description                                                               |
| ------------          |------------                                                               |
| `id`                  | Auto increment id field                                                   |
| `user_id`             | User id to be notified                                                    |
| `item_id`             | Comment id where the @mention was discovered                              |
| `secondary_item_id`   | Contact, Group, Location, etc that the comment was logged against         |
| `component_name`      | "comment"                                                                 |
| `component_action`    | "mention"                                                                 |
| `date_notified`       | date and time that this event occurred                                    |
| `notification_note`   | optional notes field                                                      |
| `is_new`              | boolean status of whether the user has viewed the notfication or not      |


### Assigned To, Update Needed, Baptism Added

| Column Name           | Description                                                               |
| ------------          |------------                                                               |
| `id`                  | Auto increment id field                                                   |
| `user_id`             | User id to be notified                                                    |
| `item_id`             | The Activity Log key id of the event (`$wpdb->dt_activity_log`(`histid`) |
| `secondary_item_id`   | Contact, Group, Location, etc that the comment was logged against         |
| `component_name`      | `field_update`, `follow_activity`                                         |
| `component_action`    | `assigned_to`, `update_needed`, `baptism`                                 |
| `date_notified`       | date and time that this event occurred                                    |
| `notification_note`   | optional notes field                                                      |
| `is_new`              | boolean status of whether the user has viewed the notfication or not      |


