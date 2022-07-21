
## Workflows




### Gitlab branches structures:

MH team creates the project through pipeline by filling severals variables to initiate gitlab repository and kubernetes infrastructure. 
To this, 2 branches are available for web agencies.

Three distincts environments are available to test, validate and deploy.
* **UAT** is an independant stage to validate functionnal release.
* **Staging** is a mandatory step before to deploy in production.
* **Production** is the final environment.

The relations between Git branches and environments.
* uat           --> [UAT](uat-api.moethennessyvietnam.com.vn)
* master        --> [Staging](stg-api.moethennessyvietnam.com.vn)
* production    --> [Production](api.moethennessyvietnam.com.vn)

[UAT / Master] For each project,

| action  | result |
| ------------- | ------------- |
| Developper pushes in uat  | it does a `build image` that build the image and deploys in `uat`  |
| Developper pushes in master  | it does a `build image` that build the image and deploys in `stg` |

[To deliver in production]

The Agency management pipeline deploys in `production`. [in fact, it is the image created from the result of the push process to the `master` branch, and deployed in the `stg` environment.]

**Do not forget that you need to deploy in staging before to update production.**

## Deployments

### Staging environment

Clone the agency repository where future commits will be done.

```
git clone https://gitlab.moet-hennessy.net/mhis/dynamic/agency/api-moethennessyvietnam.git
```

Copy your source files.

```
git add .
git commit -m "Initial commit"
git push -u origin master
```

Try to observe the beginning of the pipeline in the [console](https://gitlab.moet-hennessy.net/mhis/dynamic/operator/api-moethennessyvietnam/pipelines)
Later, you can connect to https://stg-api.moethennessyvietnam.com.vn

### Production environment

The goal is to commit any files so we will use a changelog to explain why we want to deploy in production.

```
git checkout production
echo "..." > CHANGELOG.md
git add CHANGELOG.md
git commit -m "deploy to production"
git push origin production
```

Try to observe the beginning of the pipeline in the  [console](https://gitlab.moet-hennessy.net/mhis/dynamic/operator/api-moethennessyvietnam/pipelines)
Later, you can connect to https://api.moethennessyvietnam.com.vn

### UAT environment

Copy your source files.

```
git checkout uat
git add .
git commit -m"Deploy to UAT"
git push origin uat
```

Try to observe the beginning of the pipeline in the [console](https://gitlab.moet-hennessy.net/mhis/dynamic/operator/api-moethennessyvietnam/pipelines)
Later, you can connect to https://uat-api.moethennessyvietnam.com.vn

