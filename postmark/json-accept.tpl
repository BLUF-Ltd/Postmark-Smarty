{* 
 * BLUF 4.5 email template - member acceptance & welcome
 *
 * This template builds the JSON data for the Postmark template
 *
 *}
 {
	 "subject": "{#welcome_subject#}",
	 "header": "{eval var=#welcome_header#}",
	 "body": [
		 {
		 "line": "{#welcome_1#}"
		 },
		 {
		 "line": "{eval var=#welcome_2#}"
		 }
	 ],
	 "button": [
		 {
			 "link": "https://www.bluf.com/setpass/{$setpass}",
			 "text": "{#welcome_button#}",
			 "button_problem": "{#button_problems#}"
		 }
	 ],
	 "lower": [
		 {
			 "line": "{#welcome_b1#}"
		 },
		 {
			  "line": "{#welcome_b2#}"
		 },
		 {
		   "line": "{#welcome_b3#}"
		},
		{
			"line": "{#welcome_b4#}"
		}
	 ],
	 "signoff" : "{#welcome_signoff#}",
	 "signed" : "{#welcome_signed#}",
	 "blacklist" : [ null ]
 }