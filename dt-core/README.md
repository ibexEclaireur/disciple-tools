# DT-Core
The D.T. core files contain most of the shared libraries, utilities, integrations, and language files. The organizational 
rule of thumb is that if it is shared beyond more than one module, it would be placed in the core to be available to all modules.

## Core Folders and Files
1. admin/ _(This folder holds all the activation, deactivation, roles, privacy, and general option files.)_
1. css/ _(This folder holds the admin panel css files.)_
1. img/ _(This folder holds the admin panel image files.)_
1. js/ _(This folder holds the admin panel javascript files.)_
1. integrations/ _(This folder holds the integrations to analytics and facebook.)_
1. languages/ _(This folder holds traslation files.)_
1. libraries/ _(This folder holds the key Post-2-Post library which does all the work of connecting post types, and contains
a google analytics library.)_
1. logging/ _(This folder contains the key activity hooks that catch changes to contacts other records in the system.)_
1. metaboxes/ _(This folder holds a number of shared metaboxes that are used in the admin panels on the post type pages.)_
1. class-taxonomy.php _(This file is a resource to all post types to create a taxonomy for that post type.)_
1. config-p2p.php _(This file configures the P2P library and makes the connections between the different post types.)_
