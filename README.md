Likeable
========

Likeable behaviour for Yii powered PHP+MySQL application

To handle likes in this version of the extension, a database of yours should have at least 2 entites: items and users.
There should also be a third table to handle many-to-many relationships - actual likes.

Table structure: items_users (item_id, user_id)

The idea is very simple:

When a user likes an item, the extension creates a new record in that table.
When the user dislikes the item, the extension deletes the record from the 'items_users' table.

Ext Initialization:

In your item model class

function behaviors() {
        return array(
            //makes the model likeable only if the user is not a guest.
            'likeable' => array(
                'class' => 'ext.likeable.CLikeableBehavior',
                //Many-to-many table for a users-items relationship
                //where all likes are stored.
                'items_users_table' => 'items_users',
                //Name of the item id column
                'item_id_column_name' => 'item_id',
                //Name of the user id column
                'user_id_column_name' => 'user_id'),
}              

Usage:

In your item controller do something like this: Item::model()->like($model->item_id);