.PHONY: configure install start

# Règles obligatoires

configure: install
	# Configure app
	ant configure build -Dprofile=developer-portal

install:
	# Install app dependencies (composer, npm, etc)
	./composer.phar install
	./composer.phar install -d src/web/forum/extensions/constructions-incongrues/vanilla-ext-bookmarklet
	./composer.phar install -d src/web/forum/extensions/constructions-incongrues/vanilla-ext-fields

start:
	# Start service
	# Dependencies are declared by adding a DEPENDENCIES= string at the end of the command
	# eg. $(MAKE) -C ../../../.. musiquesincongrues-start-service DEPENDENCIES="rabbitmq ws"
	$(MAKE) -C ../../../.. musiquesincongrues-start-service DEPENDENCIES="dbgp-proxy urlinfo"

# Règles propres au projet

# Vous pouvez définir des règles supplémentaires, propre au cycle de vie du projet
# Lors du développement, ces règles pourront être exécutées dans le contexte d'un container via
# la règle `<service>-make` : https://github.com/ARAMISAUTO/developer-portal/blob/master/README.md#service-make