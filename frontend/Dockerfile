FROM node:22.0.0 as BUILDER

WORKDIR /usr/src/frontend

COPY . .
RUN rm -rf node_modules package-lock.json

RUN npm i -g nuxt
RUN npm i && npm run build 

ENV NITRO_PORT=80
ENV NITRO_HOST=0.0.0.0
CMD node .output/server/index.mjs