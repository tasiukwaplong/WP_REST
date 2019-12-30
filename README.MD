## WP_REST

This PHP class will enable getting data from Wordpress site. It extracts data from sql database.

## HOW TO MAKE A CALL
To make a call to the API use: 

* ``http://YOUR_URL/?call=getAllPosts`` --To get all posts from WP project
* ``http://YOUR_URL/?call=getPost/id=12`` --To get a particular post content from the WP project with ID of 12

## RESPONSE
On successful call to get all posts e.g: ```http://YOUR_URL/?call=getAllPosts``` the response that will be returned will be: 
```JS
{
    data: {
        errorExist: false,
        body: [{
            ID: "36",
            post_title: "Post title goes here",
            post_content: "Post conetent goes here. It usually contains <html> tags in it",
            imageLink: "2019/12/1.jpg"
        }, {
            ID: "33",
            post_title: "Another post title goes",
            post_content: "This post has no featured image so will be null",
            imageLink: null
        }]
    }
}

```

On error like calling a post that does not exist, e.g call to ```http://YOUR_URL/?call=getPost&id=300``` and post with ID 300 does not exist; it will result to:
```JS
{
    data: {
        errorExist: true,
        error: "Sorry, this post does not exist"
    }
}
```
Replace ```http://YOUR_URL/``` with [http://nacoss-ful.000webhostapp.com/api](http://nacoss-ful.000webhostapp.com/api) to test the API