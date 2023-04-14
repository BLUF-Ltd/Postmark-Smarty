{* 
 * BLUF 4.5 email template - member deleted
 *
 * This template builds the JSON data for the Postmark template
 *
 *}
 {
	 "subject": "{#deleted_subject#}",
	 "header": "{#deleted_header#}",
	 "body": [
		 {
			 "line": "{#deleted_1#}"
		 },
		 {
			 "line": "{#deleted_2#}"
		 },
		 {
			 "line": "{#deleted_3#}"
		 },
		 {
			  "line": "{#deleted_4#}"
		  },
		  {
				"line": "{#deleted_5#}"
		}  
	 ],
	 "button" : [ null ],

	 "lower": [
		 null
	 ],
	 "signoff" : "{#reject_regards#}",
	 "signed" : "{#signed_team#}",
	 "blacklist" : [ null ]
 }