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

    <br>
    <br>
    <h1>$BackdoorCTF bf-captcha-revenge-web-150</h1>
    <br>
</center>
<p>
<br>
<br>
<br>

I recently decided to check out the <a href="https://backdoor.sdslabs.co/">BackdoorCTF</a> challenge.  This is the writeup for the Web 150 challenge named "bf-captcha-revenge".
</p>
<img src="res/web150_0.png" \>

<p>
The webpage: 
<p>
<img src="res/web150_1.png" width="50%" height="50%"  \>

<p>
Upon opening the page, I recognized the "brain-fucking" string as a hint to the <a href="https://en.wikipedia.org/wiki/Brainfuck">Brainfuck language</a>.  Running the string through a <a href="https://sange.fi/esoteric/brainfuck/impl/interp/i.html">brainfuck interpreter</a>, I got the following variaties of decoded output: 
<p>
<script src="https://gist.github.com/ehrenb/087bc643b377b229bc3be97c1022c97b.js"></script>

<p>
The expressions evaluate to very large numbers which, when computed and entered with a correct captcha, allow you to progress to the next set of brainfuck+captcha challenge.  I was given the first captcha for free, but after getting through one captcha and decoding, I had to decipher the audio myself to progress.  The audio is sped up quite a bit, but can be slowed down to a decipherable speed using Firefox or opening the file with VLC.  I was actually able to get through 16 rounds manually without error.  While trying to solve it manually, I realized that one of the audio files that I saved was a duplicate.  The file names appear to be MD5 hashes of something, but they are unique, so it's possible to scrape the initial page with the "free" captcha number many times and create a mapping of all audio file names to their known integer values.  Enter Python requests:
<p>
<script src="https://gist.github.com/ehrenb/97317a2953637fc9578e6f333b802b4d.js"></script>

<p>
I had to run this for about 45 minutes to accumulate enough mappings to feed the fully automated solver.  I ended up with 996 unique mappings.  
<p>
The brainfuck code can be deciphered in Python and saved to a string using one of the many libraries available (I ended up using <a href="https://github.com/fboender/pybrainfuck">pybrainfuck</a>).  After many failed of attempts of string parsing, I wrote the following automated solver which reads the mapping created by the previous script, parses and evaluates the brainfuck code and submits the data via POST.  This needed to be <a href="http://docs.python-requests.org/en/master/user/advanced/">sessionized</a> to retain progress to advance to the next round.  

<p>
<script src="https://gist.github.com/ehrenb/4d87841ec997f5b4a70ab51d5d4aced2.js"></script>

<p>
In conclusion, the challenge required 499 succesful solves, and on the 500th round, it prints the flag:

<img src="res/web150_2.png" width="75%" height="75%" \>

</p>

</html>
