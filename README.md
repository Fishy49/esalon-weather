# eSalon Weather

You hair is important. Please check the weather.

## Setup

1. Create the `geocodes` table in a MySQL DB named `esalon_weather` like so:
```
CREATE TABLE `geocodes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `weather_data` mediumtext,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP,
  `weather_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `ip_address` (`ip_address`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
```
2. Run the thing: `bin/cake server -p 8765`

## Check the weather

Simply punch in your IP address in the form provided and watch the magic.

_Completed in 5 hours_
