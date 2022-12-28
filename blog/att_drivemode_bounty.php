<html>
<!--        <link rel="stylesheet" href="minimal.css"> -->
<!--	<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script> -->
<title>Blog</title>
<?php include 'header.html'; ?>
<br>
<br>
<br>
<center>
    <br>
    <br>

    <br>
    <br>
    <h1>$AT&T Drivemode Android Bug Bounty</h1>
    <br>
</center>

<p>
<br>
<br>
<br>
Below is the discovery for a vulnerability in AT&T's Drivemode Android application. The bug is now remediated.

<h2>Vulnerable Website URL or Application</h2>
https://play.google.com/store/apps/details?id=com.drivemode&amp;amp;feature=nav_result
<br>
<h2>Description of Security Issue</h2>
https://play.google.com/store/apps/details?id=com.drivemode&amp;amp;feature=nav_result 
<br>
This report is for the Android AT&T DriveMode application (Version 1.0.4.2, Version Code 1030570). Tested on a Google Nexus 5 with Android 5.0. When choosing a new contact to import to the Allow List, the application stores the inputted data in a ContentProvider located at: &quot;content://com.drivemode.contentprovider.ContactsProvider/allowlist&quot;. This ContentProvider is unprotected, allowing any application with knowledge of the Provider URI to query it for data without any additional permissions. This allows for name and phone number leakage of the contact list if the user chooses to import their existing contacts.

<h2>Exploit Scenario of Vulnerability</h2>
An existing application on the phone may retrieve a Cursor to the ContentProvider and iterate it to retrieve the data stored (See code snippet).
<br>
<h2>Steps needed to reproduce bug</h2>
<br>
1. Start the Drivemode application. 
<br>
2. Insert/import at least one contact into the Allow List. 
<br>
3. Run the snippet below in a separate, unprivileged Android application and view the Logcat output to show the data leakage.
<br> 
<script src="https://gist.github.com/ehrenb/23387e25f19512720de565a955bb2425.js"></script>
</html>
