--Insert DataSet
delete from .dbo.users
delete from .dbo.visits
delete from .dbo.cities
delete from .dbo.states

DBCC CHECKIDENT ('.dbo.users',RESEED,0);   
DBCC CHECKIDENT ('.dbo.visits',RESEED,0);   
DBCC CHECKIDENT ('.dbo.cities',RESEED,0);   
DBCC CHECKIDENT ('.dbo.states',RESEED,0);   

insert into users (firstname, lastname, datetimeadded, dateadded, lasttimeupdated)
select firstName, LastName, datetimeadded, dateadded, lastupdated
from users_csv


insert into states (state_name, abbreviation, datetimeadded, dateadded, lasttimeupdated)
select Name, abbreviation, datetimeadded, dateadded, lastupdated
from state_csv

insert into cities (city_name, state_id, status, latitude, longitude, datetimeadded, dateadded, lasttimeupdated)
select Name, stateid, status, latitude, longitude, datetimeadded, dateadded, lastupdated
from city_csv