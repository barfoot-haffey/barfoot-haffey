CREATE VIEW `view_experiment_users` AS (
	SELECT  `eb`.`name` AS `name`,  
		`eb`.`experiment_id` AS `experiment_id`,  
		`eb`.`published_id` AS `published_id`,  
		`eb`.`location` AS `location`,  
		`ub`.`email` AS `email`,  
		`ub`.`user_id` AS `user_id` 
	FROM (((`experiments` `eb`  JOIN `contributors` `cb`  
		ON ((`eb`.`experiment_id` = `cb`.`experiment_id`)))  JOIN `users` `ub`
		ON ((`cb`.`user_id` = `ub`.`user_id`))))) 