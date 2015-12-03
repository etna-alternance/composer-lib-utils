<?php

putenv("APPLICATION_ENV=testing");

putenv("TEST_LIBUTILS_SPRINTER_ROUTING_KEY=sprinter.lefran_f");

putenv('RABBITMQ_URL=http://guest:guest@localhost:5672');
putenv('RABBITMQ_VHOST=/test-behat');

