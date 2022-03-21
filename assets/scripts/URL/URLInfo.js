export let URLInfo = function() {
    this.getReviewID = function(){
        let location = window.location.pathname;
        return (location.match(/id(\d+)$/i)[1]);
    }

    this.getLocale = function(){
        let path = window.location.pathname;
        return path.match(/^\/(ru|en)\//i)[1];
    }

    this.cloudinaryPath = 'https://res.cloudinary.com/ht74ky0yv/image/upload/v1638384344/';
}