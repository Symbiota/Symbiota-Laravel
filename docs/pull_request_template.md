# Pull Request Checklist During the Blitz Phase:

# Pre-Approval, Blitz Phase

- [ ] Comment which GitHub issue(s), if any does this PR address
- [ ] If more description is required, add a description section
- [ ] Are (basic) tests written to cover the new functionality?
- [ ] If there is text in this PR that has not been internationalized, implementing this is not required; add a @TODO comment and create a github issue about it.
- [ ] There are no linter errors
- [ ] Has Pint been run?
- [ ] Have the required updates to the .env.example been made?

# Code Review, Blitz Phase
- [ ] QA not required, but go ahead and do if the situation requires it
- [ ] PR should be squash merged
- [ ] Code reviewer can go ahead and merge a PR after approving the code review
- [ ] Don't forget to delete your feature branch upon merge. Ignore this step as required.

#  Pre-Approval, Post Blitz Phase
- [ ] @TODO add everything above
- [ ] New features have responsive design (i.e., look aesthetically pleasing both full screen and with small or mobile screens)
- [ ] All new text is preferably internationalized

#  Post-Approval, Post Blitz Phase
- [ ] If this PR makes any changes that would require additional configuration of any Symbiota portals outside of the files tracked in this repository, make sure that those changes are detailed in [this document](https://docs.google.com/document/d/1T7xbXEf2bjjm-PMrlXpUBa69aTMAIROPXVqJqa2ow_I/edit?usp=sharing)
- [ ] TODO discuss: increment the Symbiota version number in the symbase.php file and commit to the `hotfix` branch