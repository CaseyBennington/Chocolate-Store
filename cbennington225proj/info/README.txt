To get this up and running please follow the directions listed below.

1. Extract this file into c:\xampplite\htdocs, or import into NetBeans to
    same directory.
2. Be sure xampplite is running.
3. Import the sql file named: cbennington225proj.sql.
4. Create a new user, itp225 with the password itp225 for the host localhost.
5. Edit the mysqli_connect.php settings to match the database name and login info.
6. Edit the config.inc.php settings to match your own information to enable this program's email functionality.
7. Be sure to match your server's email configuration settings if you upload this program to your website or properly configure xampp sendmail functionality.
8. You will need to change the mail server email account to your own.
8. Browse http://localhost/cbenningtonprojectITP225/
9. index.php is intended as the starting page for this program. Should the user browse to a different page without being logged in, they will be redirected to this page.
10. Upon the user's first time to this site, they will not have a username or password and therefore will have no other working option but to register.
11. The user will fill out a few details about themselves and be emailed an activation code. They will have to click on this randomly generated code in that email in order to be activated.
12. After the user is activated they can then log in.
13. After logging in, the user will be at the main site for this program, which is the ordering page.
14. The user can use this page to complete their customer information, complete their order form and enter their payment information.
15. This page has been validated and requires properly entries on all inputs.
16. After the user has properly entered their information and order they can submit this information to the checkout page for processing.
17. The checkout page will calculate their order and update the proper database tables.
18. The user will see a receipt for their order and will also receive an email with this information.
19. That is the basic functionality of this program.
20. The user can also change their password and if they have not logged in and forgot their password, they can receive an email with that information.
21. If the user is properly authorized by management to be an admin, they will be granted access to additional options.
22. Clicking the administration link will take the proper user to a subsection of the site with options to view customers, products, and orders.
23. These lists are all paginated.
24. Further, the user has the ability to edit the available products. They can edit the current listing of products by changing a current product's data, like name or inventory.
25. They can also delete current products.
26. They can also add additional products.
27. The user can then log off when they have finished their business.