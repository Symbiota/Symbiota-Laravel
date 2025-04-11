# User Profile

## Profile

### Metadata Form
Includes fields:
- name
- email
  - New email must be verified
- guid
- title
- institution 
- city
- state 
- zip
- country
### Delete Profile Button
- Must have delete button shown
- On delete a confirmation must be present to the user

## Projects and Checklists

### Create Checklist
- If user has `UserRole` of `ClCreate` then show create checklists button

### Show user checklists
Show checklists that the user has `UserRole` of `ClAdmin`

Show list of checklists with the following info 
#### Required
- Checklist Name
- Link to Checklist Profile
- Link to Checklist Admin
- Link to Checklist Voucher Admin
#### Optional
- Abstract
- Locality

### No checklists
- Display a message stating their are no checklists with user permissions to show

## Collections

### Create Collection
- If user has `UserRole` of `ClCreate` then show a create collection button

### Display User Collections 
Show collections that the user has `UserRole` of `CollEditor` or `CollAdmin`

Show list of collections with the following info 
- Collection Icon (If exists)
- Collection Name
- Users Collections Permissions
- Link to collection profile

### No Collections
- Display a message stating their are no collections with user permissions to show

## Datasets

### Create Dataset 
- Show a create dataset button to all users

### Display User Datasets 
Show collections that the user has a matching `uid` in `omoccurdatasets`

Show list of datasets with the following info:
- Name (use field `name` not `datasetName`)
- Notes 
- Creation date
- Link to dataset manager

### No Datasets 
- Display a message stating their are no datasets with user permissions to show

## Passwords and Authentication

### Password Reset Form
Form with following fields:
- `current_password` for users currently accepted password
- `password` for users new password
- `password_confirmation` to confirm users new password

Display Errors If:
- `password` and `password_confirmation` don't match
- `current_password` is not correct
- `password` TODO with required minimum password traits

### Two Factor Authentication
Display the Following Steps:

#### Enable Two Factor
1. Display button to enable two factor authentication
2. Show QR code to use for two factor authentication application
3. Required user to input code generated from their connected two factor application in an input with the name `code`
4. Upon success display recovery codes and a message telling the user to save them somewhere save because they will not be displayed again

#### Disable Two Factor
If two factor is enabled:
- Display a button to disabled two factor authentication

## Developer 
### Create Personal Access Token
- Display a generate token button to all users

Upon clicked the button display a form with the following fields:
- Text input `token_name` 
- Date input `expiration_date` 
- Button to submit form
- Button to cancel form 

#### On Success
- Display sucess message
- Display api key
- Display message stating that the api key will only be displayed this one time

#### On Error
TODO (Logan) flesh out error requirements

### Show Personal Access Token 
If the user has personal access tokens then display them with the following information:
- Tokens name (via `name` field)
- Tokens `expiration_date` or message stating token never expires
- Tokens `ablities` which are currently always `*` indicating everything
- Delete button which deletes token

### No Tokens
- Display a message stating their are no personal access tokens
