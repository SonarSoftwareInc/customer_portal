# Upgrading
We've applied some framework upgrades to the Sonar Customer Portal in order maintain long term support and security.

If you are setting up a new instance of the customer portal, you will automatically be running the updated version and no further action is required.


If you are running an existing deployment of the customer portal, manual action is required:

1. Stop the customer portal by running
	```
	sudo docker-compose down
	```

2. Update the docker-compose.yml file so that
	the image tag points to
	```
	image: sonarsoftware/customerportal:next
	```

3. Start the customer portal
	```
	sudo docker-compose up -d
	```
4. Verify that your customer portal is functional by visiting it in a browser. If you are able to load and navigate around, it should be good to go!

	a. If it does not work, you can revert by repeating steps 1 through 3, but changing the image tag back to 
	```
	image: sonarsoftware/customerportal:stable
	```
	b. Reach out to support@sonar.software for further assistance.

