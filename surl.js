function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (err) {
        return false;
    }
}

const URL_API = "https://www.dorompa.tokyo/playground/shortenurl.php";

Vue.use(Toasted);

const vm = new Vue ({
    el: '#app',
    data: {
        inputUrl:'',
        placeholder:'Enter URL to be shortened',
        results: []  //    { long: 'long URL', short: 'shortened URL' }
    },
    computed: {
    },
    methods: {
        shortenUrl: function(longUrl) {
            if (longUrl.length == 0) {
                return;
            }
            if (!isValidUrl(longUrl)) {
                this.$toasted.error('Invalid URL.', {position:'top-center'}).goAway(2000);
                return;
            }
            let params = {params:{url: longUrl}};
            axios
                .get(URL_API, params)
                .then(res => {
                    this.message = res.data.message;
                    if (res.data.code != 200) {
                        this.$toasted.error(this.message, {position:'top-center'}).goAway(2000);
                    }
                    else {
                        if (res.data.exists) {
                            this.$toasted.info('Specified URL is already shortened.', {position:'top-center'}).goAway(2000);
                            let found = false;
                            for (var i = 0; i < this.results.length; i++) {
                                if (this.results[i].long == this.inputUrl) {
                                    found = true;
                                    break;
                                }
                            }
                            if (!found) {
                                this.results.unshift({ long: this.inputUrl, short: res.data.shortUrl });
                            }
                        }
                        else {
                            this.results.unshift({ long: this.inputUrl, short: res.data.shortUrl });
                        }
                    }
                    this.inputUrl = '';
                });
        },
    },
    filters: {
        truncate: function (text, length, suffix) {
            if (text.length > length) {
                return text.substring(0, length) + suffix;
            } else {
                return text;
            }
        },
    }
})
