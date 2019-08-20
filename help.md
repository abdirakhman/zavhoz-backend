login.php
  Need:
    email, pass
  Function:
    Auth and give jwt tokens (access and refresh) and expiration of access_token
  Return:
    error, access_token, refresh_token, good_before, success
refresh.php
  Need:
    token (refresh_token)
  Function:
    Refresh expired access_token. Give new pair of tokens and expiration of access_token
  Return:
    error, access_token, refresh_token, good_before, success
constants.php
  Function:
    contain constants
get_max_id.php
  Function:
    Gives all count of furniture
  Return:
    error, id
get_staff.php
  Function:
    Gives list of all staff
  Return:
    error, return_array
insert.php
  Need:
    init_cost, name, arom_price, responsible, place, date, month_expired
  Function:
    Insert new item in DB.
  Return:
    error
JWT.php
  Please go to jwt.io
register.php
  Need:
    login, pass, name, place, type, code
  Function:
    register new user
  Return:
    error
request_place_history.php
  Need:
    id
  Function:
    Give place history of item
  Return:
    error, place_history
request_place_history.php
  Need:
    id
  Function:
    Give responsible history of item
  Return:
    error, responsible_history
request_staff_history.php
  Need:
    id
  Function:
    Give responsibility history of staff person
  Return:
    error, history
request.php
  Need:
    id
  Function:
    Return all information about item
  Return:
    error, init_cost, name, arom_price, responsible, place, date, month_expired
update.php
  Need:
    id, responsible, place
  Function:
    Updates information
  Return:
    error
validate.php
  Function:
    Validate access_token
get_place.php
  Function:
    Give list of places
  Return:
    error, return_array
