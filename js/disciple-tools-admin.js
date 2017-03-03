
function findGroupUsers(group_id) {
    jQuery.post(
        ajaxurl,
        {
            'action': 'list_group_users',
            'data':   group_id
        },
        function(response) { alert(response)}

    );
}

