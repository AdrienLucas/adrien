FROM adrienlucas/debian-jessie:latest
MAINTAINER Adrien Lucas <adrien.lucas@sensiolabs.com>
CMD sh /tmp/init-container.sh

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update

ADD init-container.sh /tmp/init-container.sh
ADD nginx-vhost.conf /etc/nginx/sites-available/app
RUN ln -s /etc/nginx/sites-available/app /etc/nginx/sites-enabled/app