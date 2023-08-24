<p align="center"><a href="https://symbiota.org/" target="_blank"><img src="https://symbiota.org/wp-content/uploads/LogoSymbiotaPNG-1024x682.png" width="400" alt="Symbiota Logo"></a></p>

WIP Test Repo to port Symbiota over to the [Laravel Framework](https://laravel.com/) to make use of its rich feature set.

### Goals
- Make Laravel Version integrate with Current repo [BioKIC/Symbiota](https://github.com/BioKIC/Symbiota)
- Improve General Performance of Symbiota
- Simply and Improve existing patterns

## Integrating with Current Symbiota
1. Create a folder `./legacy/` in the root directory
2. Clone [BioKIC/Symbiota](https://github.com/BioKIC/Symbiota) or existing fork into `./legacy/` folder
3. Remove/Comment all references to `symbini.php` in your legacy repo. The easiest way is to use ripgrep or grep with sed: <br/>
Mac: `rg '^.*(symbini).*$' -l | xargs sed -i '' '/^.*symbini.*$/ s/^/\/\//g'` <br/>
Other: `rg '^.*(symbini).*$' -l | xargs sed -i '/^.*symbini.*$/ s/^/\/\//g'` <br/>
4. Should now file route unknown routes to legacy folder.

## Porting Pages

### Styling
You must set `VITE_CSS_TARGET` variable in your `.env` to your legacy css path like `VITE_CSS_TARGET="legacy/css/v202209/symbiota/main.css"`. Note that if you have legacy css that isn't include into main.css or by a file imported into main css it will not be included in vites build.
