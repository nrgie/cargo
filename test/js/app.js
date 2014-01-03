if ($APP === void 0) var $APP = {};

$APP.Event = {
    document: $(document),
    bind: function(t, n) { return this.document.bind(t, n), this },
    trigger: function(t, n) { return this.document.trigger(t, n), this },
    off: function(t, n) { return this.document.off(t, n), this }
};

$APP.Router = {
    historyList: [],
    routesCallback: {},
    userNavigation: !0,
    pagesScrollPosition: {},
    pageCache: {},
    lastPage: null,
    reloadPage: !1,
    init: function() {
        $APP.Router.History = window.History;
        var t = History.getState();
        $APP.Router.lastPage = t.url, $APP.Router.historyList.push(window.location.href), $APP.Router.canCachePage(window.location.href) && $APP.Router.cachePage(window.location.href, {
            html: $("#contentWrapper").html()
        }), $APP.Router.History.Adapter.bind(window, "statechange", this.onUrlChange.bind(this)), this._initLinkObserver(), this._initFormSubmitObserver();
        var e = this;
        $(window).scroll(function() {
            e.pagesScrollPosition[window.location.href] = $(window).scrollTop()
        })
    },
    onUrlChange: function() {
        var t = History.getState(),
            e = t.url;
        if ($APP.Router.lastPage !== e) {
            if ($APP.Router.userNavigation) $APP.Router.historyList.push(e);
            else {
                var a = $APP.Router.historyList.lastIndexOf(e); - 1 !== a && a === $APP.Router.historyList.length - 2 && $APP.Router.historyList.splice(a + 1)
            }
            if ($APP.Router.lastPage = e, !$APP.Router.userNavigation) return window.location.href = e, void 0;
            $APP.Event.trigger("url:change", e), $APP.Router.userNavigation = !1, $APP.Api.loadPage(e), "undefined" != typeof _gaq && _gaq.push(["_trackPageview", e])
        }
    },
    go: function(t, e) {
        ("function" == typeof e || "string" == typeof e) && (this.routesCallback[location.origin + t] = e), $APP.Router.History.pushState(null, null, t)
    },
    back: function(t, e) {
        var a = $APP.Router.historyList,
            r = a[a.length - t - 1];
        r && !e ? ($APP.Router.userNavigation = !0, $APP.Router.History.go(-t)) : window.history.back(-t)
    },
    reload: function() {
        window.location.reload()
    },
    callback: function(t) {
        "function" == typeof this.routesCallback[t] ? this.routesCallback[t].apply() : "string" == typeof this.routesCallback[t] && $APP.Event.trigger(this.routesCallback[t])
    },
    cachePage: function(t, e) {
        this.pageCache[t] = e
    },
    getPageCache: function(t) {
        return this.pageCache[t] !== void 0 ? this.pageCache[t] : null
    },
    canCachePage: function(t) {
        return !1
    },
    clearPageCache: function(t) {
        return this.pageCache[t] ? (delete this.pageCache[t], !0) : !1
    },
    _initLinkObserver: function() {
        $("body").delegate("a", "click", function(t) {
            var e = $(this),
                a = e.attr("rel"),
                r = e.data("confirm");
            if (r) {
                var i = confirm(r);
                if (!i) return t.preventDefault(), void 0
            }
            var o = e.data("before");
            switch (o && $APP.Event.trigger(o, e), a) {
            case "page":
                History.emulated.pushState || (t.preventDefault(), window.scrollTo(0, 1), $APP.Router.userNavigation = !0, $APP.Router.go(e.attr("href"), e.data("callback")));
                break;
            case "ajax":
                t.preventDefault(), $APP.Api.call({
                    url: e.attr("href"),
                    success: e.data("callback")
                });
                break;
            case "event":
                t.preventDefault(), $APP.Event.trigger(e.data("event"));
                break;
            case "fallback":
                break;
            default:
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                    var n = e.attr("href");
                    if (n !== void 0 && "" !== n && "#" !== n) return window.location = e.attr("href"), !1
                }
            }
        })
    },
    _initFormSubmitObserver: function() {
        $("body").delegate("form", "submit", function(t) {
            var e = $(this),
                a = e.attr("rel"),
                r = e.attr("data-update");
            if ("form" == a) {
                t.preventDefault();
                var i = {};
                $.each(e.serializeArray(), function(t, e) {
                    i[e.name] = e.value
                });
                var o = e.data("before");
                o && $APP.Event.trigger(o, i), $APP.Api.call({
                    url: e.attr("action"),
                    type: "POST",
                    data: i,
                    success: function(t) {
                        r && $(r).html(t.html);
                        var a = e.data("callback");
                        a && $APP.Event.trigger(a, t)
                    }
                })
            }
        })
    }
};

$APP.Users = {};

$APP.Api = {

    call: function(e) {
        if (e.url === void 0) return !1;
        var t = e.data || {},
            r = e.type || "GET";
        $.ajax({
            url: e.url,
            data: t,
            type: r,
            dataType: "json",
            beforeSend: function(e) {
                e.setRequestHeader("X-Requested-With", "XMLHttpRequest")
            },
            success: function(t) {
                if (t && t.ajaxify !== void 0 && t.ajaxify[0] !== void 0) {
                    var r = JSON.parse(t.ajaxify[0]);
                    if (r.forward !== void 0) return window.location.href = r.forward.url, void 0
                }
                "function" == typeof e.success ? e.success.apply(null, [t]) : "string" == typeof e.success && $APP.Event.trigger(e.success, t)
            },
            failure: function(t) {
                "function" == typeof e.failure ? e.failure.apply(null, [t]) : "string" == typeof e.failure && $APP.Event.trigger(e.failure, t)
            }
        })
    },

    loadPage: function(e) {
        var t = this,
            r = $APP.Router.getPageCache(e);
        return r ? (t.renderPage(e, r), void 0) : ($APP.AppView.selector.subPage.html("").hide(), $APP.AppView.selector.page.html("").show(), $APP.AppView.selector.loader.show(), $.ajax({
            url: e,
            type: "GET",
            dataType: "json",
            beforeSend: function(e) {
                e.setRequestHeader("X-Requested-With", "XMLHttpRequest")
            },
            success: function(r) {
                if (r.ajaxify !== void 0 && r.ajaxify[0] !== void 0) {
                    var o = JSON.parse(r.ajaxify[0]);
                    if (o.forward !== void 0) return window.location.href = o.forward.url, void 0
                }
                t.renderPage(e, r), $APP.Router.canCachePage(e) && $APP.Router.cachePage(e, r), $APP.Router.reloadPage && window.location.reload()
            },
            failure: function() {}
        }), void 0)
    },

    renderPage: function(e, t) {
        if (t.extraHtml) for (var r in t.extraHtml) if (t.extraHtml.hasOwnProperty(r)) {
            var o = t.extraHtml[r],
                a = $("#" + r);
            a.length > 0 && a.html(o)
        }
        t.html && ($APP.AppView.selector.loader.hide(), $APP.AppView.selector.page.html(t.html), $APP.Router.pagesScrollPosition[e] !== void 0 ? setTimeout(function() {
            0 === window.scrollY && $("body").scrollTop($APP.Router.pagesScrollPosition[e]), $APP.Event.trigger("scroll:heightChanged")
        }, 1) : setTimeout(function() {
            0 === window.scrollY && window.scrollTo(0, 1), $APP.Event.trigger("scroll:heightChanged")
        }, 1)), $APP.Router.callback(e)
    }
};


$APP.homeView = {

	initialized: false,
	graphinit: false,
	mode : '',
	suggestions: [],
	selector: {},

	init: function (page) {

	    this.cacheSelectors();

	    if (!this.initialized) {
		this.initObservers();
		this.initEvents();
		this.mode = 'friends';
		$('.menu button:first').addClass("active");
		this.getUsers();
		this.selector.graph.hide();
	    }

	    this.initialized = true;
	},

	/**
    	 * Caches DOM elements selectors
         */

	cacheSelectors: function () {

	    this.selector.content = $('.task-content');
	    this.selector.menuButton = $('.menu button');
	    this.selector.menu = $('.menu');
	    this.selector.usersList = $('#users');
	    this.selector.Results = $('#results');
	    this.selector.columns = $('.columns');
	    this.selector.graph = $('.graph');
	},
	
	/**
         * Initialize the selector observers
         */
	
	initObservers: function () {
	    $('body')
		.on('click', '.menu button', this.toggleMode.bind(this))
		.on('mouseenter', '#users li', this.handleHover.bind(this))
		.on('mouseleave', '.dim', this.resetDim.bind(this))
		.on('click', '.show-graph', this.toggleGraph.bind(this));
	},

	/**
         * Initialize the event observers
         */

	initEvents: function () {},

	/**
         * Toggle Visual Graph View
         */

	toggleGraph: function (e) {
	    this.selector.columns.toggle();
	    this.selector.graph.toggle();
	    this.initGraph();
	},

	/**
         * Initialize Graph View
         */

	initGraph: function () {
	    if (!this.graphinit) {
		var sys = arbor.ParticleSystem(4000, 500, 0.5, 55);
		sys.renderer = Renderer("#viewport");

		$.getJSON("?page=graph",function(data) {
	    	    var nodes = data.nodes;
	    	    $.each(nodes, function(name, info){ info.label=name.replace(/(people's )?republic of /i,'').replace(/ and /g,' & ') });
	    	    sys.merge({nodes:nodes, edges:data.edges});
	    	    sys.parameters({stiffness:600});
		});

		this.graphinit = true;
	    }
	},

	/**
         * Switch resultset filters
         */

	toggleMode: function (e) {

	    var button = $(e.currentTarget);
	    this.mode = button.data('mode');

	    // set active menu button active;
	    this.selector.menu.find("button").removeClass("active");
	    button.addClass("active");

	},

	/**
         * Reinitialize dimmed elements
         */

	resetDim: function() {
	    this.selector.Results.find("li").addClass("dim");
	    this.selector.usersList.find("li").removeClass("dim");
	},

	/**
         * function for handling element's hover event
         */

	handleHover: function (e) {
	    var $this = $(e.currentTarget);
	    var id = $this.data('userid');

	    if (e.type === "mouseenter") {
		$this.addClass("dim");
	    }

	    switch(this.mode) {
		case 'friends':
		    this.showUserFriends(id);
		break;
		case 'friendsof':
		    this.showUserFriendsOf(id);
		break;
		case 'suggest':
		    this.getSuggestions(id);
		break;
	    }
	},

	getUsers: function () {

	    $APP.Api.call({
		url: '?page=users',
		data: null,
		type: 'GET',
		success: this.getUsersCallback.bind(this)
	    });

	},

	renderUsersList: function () {

	    var users = $APP.Users;
	    var html = '';
	    for (u in users) {
		html += '<li class="user" data-userid="'+ users[u].id +'">' + users[u].firstname +' '+ users[u].surname + '</li>';
	    }
	    this.selector.usersList.html(html);
	    this.selector.Results.html(html);
	    this.selector.Results.find("li").addClass("dim");
	
	},

	getUsersCallback: function (response) {

	    if(response) {
		for (key in response) {
		    var id = response[key].id;
		    $APP.Users[id] = response[key];
		}
		this.renderUsersList();
	    } else {
		console.log(response);
	    }

	},

	showUserFriends: function (id) {
	
	    var friends = $APP.Users[id].friends;
	    this.selector.Results.find("li").addClass("dim");

	    for (key in friends) {
		var fid = friends[key];
		$("#results li[data-userid="+fid+"]").removeClass("dim");
	    }
	    console.log(friends);
	},

	isFriend: function(id, friendid) {
	    var friends = $APP.Users[id].friends;
	    var i = friends.length;
	    while (i--) { if (friends[i] === friendid) { return true; } }
	    return false;
	},

	showUserFriendsOf: function (id) {
	
	    var friends = $APP.Users[id].friends;
	    var friendsof = [];
	    this.selector.Results.find("li").addClass("dim");

	    for (k in friends) {
		var fid = friends[k];
		var a_friend_friends = $APP.Users[fid].friends;
		for (k in a_friend_friends) {
		    friendsof.push(a_friend_friends[k]);
		}
	    }

	    /** remove duplicate values from array **/
	    var temp = {};
	    for (var i = 0; i < friendsof.length; i++) temp[friendsof[i]] = true;
	    var r = [];
	    for (var k in temp) r.push(k);
	    friendsof = r;

	    for (key in friendsof) {
		var fofid = friendsof[key];
		if (fofid == id) {
    		    friendsof.splice(key, 1);
		} else if (this.isFriend(id, fofid)) {
		    friendsof.splice(key, 1);
		} else {
		    $("#results li[data-userid="+fofid+"]").removeClass("dim");
		}
	    }
	    console.log(friendsof);
	},

	cacheSuggestions: function(id, data){
	    if(data === undefined) {
		if(id in this.suggestions) {
		    return this.suggestions[id];
		} else {
		    return false;
		}
	    } else {
		return this.suggestions[id] = data;
	    }
	},

	getSuggestions: function (id) {
	    var cache = this.cacheSuggestions(id);
	    if(cache) {
		return this.showSuggestions(cache);
	    } else {
		var data = { userid: id };
		$APP.Api.call({
		    url: '?page=suggest',
		    data: data,
		    type: 'GET',
		    success: this.showSuggestions.bind(this)
		});
	    }
	},

	showSuggestions: function (response) {
	    this.selector.Results.find("li").addClass("dim");
	
	    if(response.id !== undefined) {
		this.cacheSuggestions(response.id, response.data);
		response = response.data;
	    }
	    
	    for(key in response) {
		var id = response[key].id;
		$("#results li[data-userid="+id+"]").removeClass("dim");
	    }
	}
}
