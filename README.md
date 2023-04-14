# Postmark-Smarty
Snippet: integrating Postmark with our Smarty templates for multilingual transactional emails

BLUF uses the Smarty templating engine to separate our presentation from our application, and to allow for multiple
languages to be used on the site. For each template, there will be a corresponding language file that contains the
different versions of each text for the languages we support.

The language files are effectively Smarty config files, so a section can be loaded by specifying the language code. For instance

    [en]
    EmailSubject    = Welcome to BLUF
    line1           = Congratulations, you have been accepted as a member
    line2           = Your new member number is {$memberid}
  
    [fr]
    EmailSubject    = Bienvenue à BLUF
    line1           = Félicitations, vous avez été accepté en tant que membre
    line2           = Votre nouveau numéro de membre est {$memberid}
  
In a Smarty template, these items can be inserted using a notation like this

    {#line1#}
    {eval var=#line2#}

In the second instance, the value of the $memberid variable will be inserted into the line.

This has proven a reliable way of managing our email templates over the years. However, when we switched from our previous email provider to [Postmark](https://postmarkapp.com), we had to do a bit of work.

PostMark has their own templating system, but it's not quite enough for what we wanted to do. There doesn't appear to be an easy way to 
have multiple languages, and it looked as if we would have to create multiple templates on the PostMark system, for each of our emails. That would quickly become a nightmare to manage. Plus, we already have tools of our own to handle the automation of translations, and generation of langauge files for Smarty, and didn't want to end up with a separate way of managing emails vs the rest of the site.

### The solution
Our solution was to take advantage of the fact that you can pass an array of JSON data to the PostMark API, which will be interpreted by their templating engine to create the final email. Normally, we use Smarty in our template to generate HTML pages, but it doesn't have to do that. Why not use Smarty to generate JSON instead?

So, we wrote some code that takes the parameters of an email, inlcuding the name of a template, and uses that template to generate JSON data that includes text in the desired language of the recipient. That JSON data is then passed to PostMark, where a generic template is used to generate all our BLUF transactional emails.

This snippet shows how it's done.

### What's here
The files are organised into a few top-level folders here, but in a fully working implementation, you'd need to move them to appropriate places, based on your Smarty configuration. The files in the PostMark server are designed to be cut and pasted into their template editor. (You would also probably need to edit the wrappers, like headers and footers; PostMark has some pretty clever stuff, and you can evn have email that switch between light and dark modes).

As well as the [Smarty templating engine](https://github.com/smarty-php), this system also relies upon the [PostMark client library](https://github.com/ActiveCampaign/postmark-php).

#### common
This is our main postmark library, containing the sendBLUFtransactional function, which may be called from elsewhere in the site after requiring 'core/postmark.php' 

#### language
This is the folder defined as $smarty->config_dir, and contains the postmark_texts.txt file used to provide transalations for the site templates.

#### postmark
This would normally be a subdirectory of $smarty->template_dir and contains the Smarty templates that generate JSON. For each local template named ABC there is a corresponding file json-ABC.tpl. For example, when we send the 'accept' template, the file json-accept.tpl is used to create the JSON for PostMark.

Three templates are provided in this example:
+ accept is used when a new member is accepted, and includes a call to action button
+ application is send when the application process starts, to confirm an email, and includes a call to action as well as a 'blacklist' link that stops anyone trying to enroll the same address again
+ delete is sent when an account is deleted, and contains neither call to action, nor blacklist links

#### pmserver
The files in this folder are the text of our template created on the PostMark system, and stored with the name generic-transaction. The system allows for slightly different text and HTML formats, and so we've included both here.

Not included are our constant definitions, or the KEYSpostmark.php file, which is where our PostMark API keys are stored (well away from the site document root, or anything managed by Git)

### Usage
Note that we have two 'servers' on PostMark, each with their own API key. The 'Application' server is used for dealing with member applications, sending messages as applications@bluf.com and the other server is used for all other communications, sending messages as notifications@bluf.com. Obviously, things can be a little simplified if you only have the one server.

To send a transactional message we just call sendBLUFtransactional:

    sendBLUFtransactional($address, $language, $template, $data, $application = false, $reply = false)
    
$address is the email address
$language is the language code; this must match the name of a section in the config file postmark_texts.txt; we use two letter code like en, de, fr and es
$template is the local template name to use, like 'application','accept' or 'delete' and selects which JSON template to load
$data is an array of variables that will be substituted into the JSON template
$application is boolean; when true, we use the application server, otherwise we use the main server
$reply if set, is used to create a unique mailbox hash for the reply-to field, like inbox+ABCDE@feedback.bluf.com to allow replies to be routed correctly via PostMark's [inbound message processing](https://postmarkapp.com/inbound-email).

#### Functional example
This is the code we use when starting a new member application. 

+ $email is the email entered into our systems, after a Captcha challenge.
+ $path is a random 40 character string, which is used to create the link to confirm the application.
+ $code is a verification code that the applicant must show in one of their photos.

      
      require_once('common/postmark.php') ;
      sendBLUFtransactional($email, $lang, 'application', array( 'signup' => 'https://join.bluf.com/confirm/' . $path, 'applicationemail' => $email, 'code' => $code), true) ;
     
     
So, looking at the template json-application.tpl in the postmark folder, you can see that the $signup string is directly included in the template. However, $applicationemail and $code appear instead in the postmark_texts.txt file. The Smarty eval function is used to substituted them into the template.

The end result is the JSON data that our function passes to PostMark. For details about their templates, refer to the PostMark documentation, but it's fairly straightforward - where our template sets items such as button or blacklist to null, they're omitted from the email.

The end result may perhaps not be the most elegant way of sending multiple language emails, but it does mean that there is only one template to maintain on PostMark, and all the other work, including templates and translations, is handled using the same tools as the rest of our website.

The image below shows an example application email - the logo is included via the template wrapper on the PostMark server.


![A BLUF application email as received via PostMark](/applicationemail.png "A BLUF application email as received via PostMark")
