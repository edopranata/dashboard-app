FROM node:20-bullseye


WORKDIR /app

COPY package.json ./
COPY yarn.lock ./
COPY . .

# Clean up node_modules and lockfile before install
RUN rm -rf node_modules package-lock.json yarn.lock && corepack enable && yarn install && yarn add rollup --ignore-scripts

RUN yarn global add @quasar/cli

EXPOSE 9001

CMD ["quasar", "dev", "--host", "0.0.0.0", "--port", "9001"]