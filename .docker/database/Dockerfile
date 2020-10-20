ARG MYSQL_VERSION=latest
FROM mysql:${MYSQL_VERSION}

CMD ["--character-set-server=utf8", "--collation-server=utf8_unicode_ci", "--default-authentication-plugin=mysql_native_password"]
ADD /initdb /docker-entrypoint-initdb.d

#####################################
# Set Timezone
#####################################

ARG TZ=UTC
ENV TZ ${TZ}
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && chown -R mysql:root /var/lib/mysql/


COPY my.cnf /etc/mysql/conf.d/my.cnf

RUN chmod 0444 /etc/mysql/conf.d/my.cnf

VOLUME /var/backup


CMD ["mysqld"]

EXPOSE 3306
