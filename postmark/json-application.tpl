{* 
 * BLUF 4.5 email template - application 
 *
 * This template builds the JSON data for the Postmark template
 *
 *}
 {
	 "subject": "{#apply_subject#}",
	 "header": "{#apply_header#}",
	 "body": [
		 {
			 "line": "{#apply_1#}"
		 },
		 {
			 "line": "{eval var=#apply_2#}"
		 },
		 {
			 "line": "{#apply_3#}"
		 },
		 {
			  "line": "{eval var=#apply_4#}"
		  }
	 ],
	 "button": [
		 {
			 "link": "{$signup}",
			 "text": "{#apply_button#}",
			 "button_problem": "{#button_problems#}"
		 }
	 ],
	 "lower": [
		 {
			 "line": "{#apply_b1#}"
		 }
	 ],
	 "signoff" : "{#signoff_thanks#}",
	 "signed" : "{#signed_team#}",
	 "blacklist" : [
	 	{ 
		 "link": "{eval var=#blacklist_link#}",
		 "text": "{#blacklist_text#}"
		}
	 ]
 }
