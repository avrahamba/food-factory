import fs from "fs";
import { execSync } from "child_process";

function installOrContinue() {
  const cwd = process.cwd();

  const modulesExists = fs.existsSync(`${cwd}/node_modules`);

  if (modulesExists)
    console.log(
      "\n\n *** You already have a local node_modules folder. *** \n\n\n\n"
    );
  else {
    console.log("\n\n *** INSTALLING NODE DEPENDENCIES *** \n\n\n\n");

    execSync(`cd ${cwd} && npm install`, { stdio: "inherit" });

    console.log("\n\n\n *** DONE INSTALLING NODE DEPENDENCIES *** \n\n\n\n");
  }

  startApp();
}

function startApp() {
  const cwd = process.cwd();

  console.log("\nStarting frontend service...\n");

  execSync(
    `cd ${cwd} && npm i -g nuxt && PORT=80 && nuxt dev --host 0.0.0.0 --port 80`,
    { stdio: "inherit" }
  );
}

installOrContinue();
