CREATE VIEW groups_access_menu AS
SELECT  menus.id, menus.label, menus.link, menus.parent, menus.icon, menus.sort, groups_access.group_id, groups_access.menu_id
FROM groups_access
LEFT JOIN menus ON groups_access.menu_id = menus.id