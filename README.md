# PHP Hackathon
This document has the purpose of summarizing the main functionalities your application managed to achieve from a technical perspective. Feel free to extend this template to meet your needs and also choose any approach you want for documenting your solution.

## Problem statement
*Congratulations, you have been chosen to handle the new client that has just signed up with us.  You are part of the software engineering team that has to build a solution for the new client’s business.
Now let’s see what this business is about: the client’s idea is to build a health center platform (the building is already there) that allows the booking of sport programmes (pilates, kangoo jumps), from here referred to simply as programmes. The main difference from her competitors is that she wants to make them accessible through other applications that already have a user base, such as maybe Facebook, Strava, Suunto or any custom application that wants to encourage their users to practice sport. This means they need to be able to integrate our client’s product into their own.
The team has decided that the best solution would be a REST API that could be integrated by those other platforms and that the application does not need a dedicated frontend (no html, css, yeeey!). After an initial discussion with the client, you know that the main responsibility of the API is to allow users to register to an existing programme and allow admins to create and delete programmes.
When creating programmes, admins need to provide a time interval (starting date and time and ending date and time), a maximum number of allowed participants (users that have registered to the programme) and a room in which the programme will take place.
Programmes need to be assigned a room within the health center. Each room can facilitate one or more programme types. The list of rooms and programme types can be fixed, with no possibility to add rooms or new types in the system. The api does not need to support CRUD operations on them.
All the programmes in the health center need to fully fit inside the daily schedule. This means that the same room cannot be used at the same time for separate programmes (a.k.a two programmes cannot use the same room at the same time). Also the same user cannot register to more than one programme in the same time interval (if kangoo jumps takes place from 10 to 12, she cannot participate in pilates from 11 to 13) even if the programmes are in different rooms. You also need to make sure that a user does not register to programmes that exceed the number of allowed maximum users.
Authentication is not an issue. It’s not required for users, as they can be registered into the system only with the (valid!) CNP. A list of admins can be hardcoded in the system and each can have a random string token that they would need to send as a request header in order for the application to know that specific request was made by an admin and the api was not abused by a bad actor. (for the purpose of this exercise, we won’t focus on security, but be aware this is a bad solution, do not try in production!)
You have estimated it takes 4 weeks to build this solution. You have 3 days. Good luck!*

## Technical documentation
### Data and Domain model
In this section, please describe the main entities you managed to identify, the relationships between them and how you mapped them in the database.

The application database is composed of 4 tables:

-Programmes

-Users

-Participations

-Gym rooms

For this application, the users only needed one field, which is the CNP field. It is an unique value, corresponding to a user. In order to be able to use the built in functions of the authentication package from Laravel Passport and not get any errors, the normal user fields have been kept in the database, with null values.
In order to implement administrator users, this table also contains a adminToken field, which is null for every user except admins.

In the programmes table, values from rom each programme are stored. Those are the class type, gym room id, maximum number of participants, and the time interval(two datetime values).

The participations table is a join table which contains rows of participations between users and programmes. It contains the id of the programme and the id of the user.

The gym rooms table contains only the names of the gym rooms. As it has been described in the problem statement, the rooms cannot be modified, added or removed, so the application does not contain CRUD operations for them. Because of this, the application contains a gym room factory, for initializing a certain number of rooms int the seeder each time a fresh migration happens(with '--seed' in the end). An administrator user is also implemented in the seeder.


![image](https://user-images.githubusercontent.com/48053642/151707839-77f59c26-8ada-4eb6-a852-166d541b6ae4.png)


### Application architecture
In this section, please provide a brief overview of the design of your application and highlight the main components and the interaction between them.

The application architecture of choice is Model-View-Controller, implemented in Laravel framework.The main components of the application are programmes, users, gym rooms and participations, each of them having models, and some of them having controllers as well.

###  Implementation
##### Functionalities
For each of the following functionalities, please tick the box if you implemented it and describe its input and output in your application:

[x] Brew coffee \
[x] Visualise programmes \
[x] View programme \
[x] Create programme \
[x] Delete programme \
[x] Book a programme \
[x] Cancel booking 

Visualising the programmes is available for unauthenticated users too. The authentication is based on the CNP. If a user's CNP is not memorized in the database, then he can register using a valid one. All the post functions have been implemented using the auth:api middleware, from the Laravel Passport package. The package is required in order to generate access tokens.

Every post function also has validators.

The only users that are able to use the create and delete programme function are the administrators. They have a string that is not null which acts as an adminToken.

###### Visualise programmes
The index function from the ProgrammeController is used in order to return all the programmes in the database. A programmeResource is implemented for this.

###### View programme
The view programme function requires a single parameter, the id of the programme. It uses the <i>show</i> function from ProgrammeController and returns a programme object.

###### Create programme
In order to create a programme, the administrator has to insert the parameters into the request. After validating the request items, multiple checks need to be made. The gym room in which the programme takes place needs to be empty in the time interval. If the time interval corresponds with another programme time interval, an error message will be returned. If the gym room id inside the request does not provide an existing gym room, an error message is returned. If everything is ok, a new row will be added to the programmes table.

###### Delete programme
An administrator requesting the delete action will provide the id of the programme which he wants to delete. The function <i>destroy</i> from ProgrammeController will be called. If the programme id does not correspond to a row in the table, an error message will appear. Otherwise it will be deleted from the database.

###### Book a programme
The users that want to participate to the programme will request participation. The function <i>participa</i> from UserController will be called, and then will carry the input through multiple validations. The function will check if the user is already a participant, if the programme is full of participants or not and will check if the user is a participant in any other programmes that happen at the same time. If it passes all of the validations a Participari row will be added.

###### Cancel a booking
If a user is participating in a programme, and decides to cancel it, he will need to provide the id of the programme. The system will search for rows in the table with the corresponding programme id and user id. If he finds none he recieves an error message. Otherwise, if there is a row in the table, it will be deleted and a success message will appear.

##### Business rules
Please highlight all the validations and mechanisms you identified as necessary in order to avoid inconsistent states and apply the business logic in your application.

-Two or more users cannot have the same CNP

-Two or more programmes cannot take place in the same gym room and at the same time

-A user cannot participate to two programmes that take place at the same time

##### 3rd party libraries (if applicable)
Please give a brief review of the 3rd party libraries you used and how/ why you've integrated them into your project.

##### Environment
Please fill in the following table with the technologies you used in order to work at your application. Feel free to add more rows if you want us to know about anything else you used.
| Name | Choice |
| ------ | ------ |
| Operating system (OS) | Windows |
| Database  | e.g. MySQL 8.0|
| Web server| PHP localhost |
| PHP | 8.0.1 |
| IDE | e.g. PhpStorm 2021.1.2 |

### Testing
In this section, please list the steps and/ or tools you've used in order to test the behaviour of your solution.

In order to check the application functionality I have used Postman. It was also useful to simulate the login conditions. For this, the access token needed to be inserted as a bearer token in the authorization tab.

For the GET routes, the request input was inserted in the url as parameters. For POST routes, they were inserted in the body, inside a url encoded form type.

## Feedback
In this section, please let us know what is your opinion about this experience and how we can improve it:

1. Have you ever been involved in a similar experience? If so, how was this one different?

   -This is my first anti hackaton

2. Do you think this type of selection process is suitable for you?

   I think it is very efficient. It challenges people to improve their knowledge

3. What's your opinion about the complexity of the requirements?

   The requirements are well suited for this kind of project

4. What did you enjoy the most?

   Getting to improve my programming skills, and practicing

5. What was the most challenging part of this anti hackathon?

   Learning to build my first API

6. Do you think the time limit was suitable for the requirements?

   The time limit for the application is enough for us to implement everything we know.

7. Did you find the resources you were sent on your email useful?

   Yes

8. Is there anything you would like to improve to your current implementation?

   There is room for improvement in my application:
   
   -The gym room row could have a column in which is indicated what type of programmes can take place
   
   -Some of the table names are funny
   
   -The application could support login from 3rd party applications
   
   -The update programme function could be implemented

9. What would you change regarding this anti hackathon?
   -It is as good as it gets
