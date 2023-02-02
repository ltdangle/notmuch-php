WORK IN PROGRESS...
-------------------
An opionated cli interface for notmuch / maildir emails.

What is it and why?
-------------------
Do you want to be in control of your email? Are you tired of being at the mercy of email software vendors? Good...

First thing you have to understand is that most modern email is stored in [Maildir](https://doc.dovecot.org/admin_manual/mailbox_formats/maildir/) format where each message is stored as a single file on the file system. This is as opposed to an older 'mbox' standard which stores all emails in a single file.
Already a light should go on: if your email is stored as message-per-file on remote machine, at least you should be able to download all the messages to your local machine and browse the files using available (preferable cli) tools, backup/copy/move the raw messages as you want without having to use any third-party tools.

A preferred way to download remote email is via [mbsync](https://isync.sourceforge.io/mbsync.html) created specifically for this purpose. Besides downloading, mbsync will all reflect all changes done to local files back to remote server. So if you delete an email message file locally mbsync will delete it on a remote machine as well.

But wait, it gets better. Introducing [notmuch](https://notmuchmail.org/) a cli maildir search engine that will index your Maildir archive and allow you to query it and create an equivalent of 'virtual email folders', a killer feature for many advanced email clients. Now you can search your local Maildir storage for subject, sender, etc. 

With all the heavy lifting done and your email files firmly under your control it should not be too hard to create a cli tool in a language of your choice (PHP!) that would view these raw email files. A [Symfony console](https://symfony.com/doc/current/components/console.html) application can be created to be used as a cli frontend that would display the messages found by notmuch. 

From there, you can implement message reading/composing/sending using excellent [Symfony mailer](https://symfony.com/doc/current/mailer.html). Existing familiar cli tools can be used for message viewing (`cat`) and and message composition (`vim`).

And there you have it: a hackable, straightforward cli email client written in PHP. 
