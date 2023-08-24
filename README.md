<p align="center"><a href="https://symbiota.org/" target="_blank"><img src="https://symbiota.org/wp-content/uploads/LogoSymbiotaPNG-1024x682.png" width="400" alt="Symbiota Logo"></a></p>

WIP Test Repo to port Symbiota over to the [Laravel Framework](https://laravel.com/) to make use of its rich feature set.

### Goals
- Make Laravel Version integrate with Current repo [BioKIC/Symbiota](https://github.com/BioKIC/Symbiota)
- Improve General Performance of Symbiota
- Simply and Improve existing patterns

## Integrating with Current Symbiota
1. Create folder in public with Portal name. If your portal is `seinet` then would have `./public/seinet/` as your directory path.
2. Clone [BioKIC/Symbiota](https://github.com/BioKIC/Symbiota) named portal folder
3. Add `PORTAL_NAME=` to your `.env` file and give it the name of the folder you create like `PORTAL_NAME='seinet'`
3. Success!

#### Note: Moving portal into Laravel's public folder will not make use of any of laravel's features. This step is just a means to slowly port the project in a non blocking fashion. 

## Porting Pages

### Styling
You must set `VITE_CSS_TARGET` variable in your `.env` to your legacy css path like `VITE_CSS_TARGET="public/seinet/css/v202209/symbiota/main.css"`. Note that if you have legacy css that isn't include into main.css or by a file imported into main css it will not be included in vites build.
