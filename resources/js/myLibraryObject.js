const library = {
    urlify(text) {
        if(text) {
            let urlRegex = /https?:\/\/([-\w.]+\.[a-z]{2,3})([-.A-Za-z0-9/]+)*([^\s,]*)/g;
            let matches = text.matchAll(urlRegex);
    
            if(matches) {
                for(let match of matches) {
                    let str = match[0].replace(/\//g, '%;').replace(/\?/g, '``');
                    text = text.replace(match[0], `<a href="/away/${str}" target="__blank" style="overflow-wrap:anywhere; display: inline-block;" class="hover:underline decoration-indigo-400 decoration-2 text-indigo-400">${match[1]}${match[2]}</a>`);
                }
            }
        }
    
        return text;
    },
    roundValue(value) {
        if(value > 999) {
            value = (value / 1000).toFixed(1) + "K";
        }
    
        return value;
    },
    isOnline(timestamp, timezone = 0) {
        let last_seen = Date.parse(timestamp) + 60000*60*timezone;
        let now = Date.now();
        let diff = now - last_seen;
    
        return diff < 60000;
    },
    clearErrors(form) {
        setTimeout((form) => {  
            form.clearErrors();
        }, 10000, form);
    },
    textAreaAdjust(element) {
        element.style.height = "1px";
        element.style.height = (element.scrollHeight) + "px";
    },
    copyData(event, form) {
        let items = event.clipboardData.items;
        let files = event.clipboardData.files;
    
        if(files.length > 0) {
            for (let index in files) {
                if(typeof files[index] == "object") {
                    form.add_files[index] = files[index];
                }
            }
            validateFiles(form);
        } else {
            for (let i = 0; i < items.length; i++) {
                if (items[i].type === "text/plain") {
                    items[i].getAsString((string) => {
                        setTimeout(() => {
                            createLinkObject(form, string);
                        }, 0, form, string);
                    });
                }
            }
        }
    },
    dropData(event, form) {
        event.preventDefault();
        let items = event.dataTransfer.items;
        let files = event.dataTransfer.files;
    
        if(files.length > 0) {
            for (let index in files) {
                if(typeof files[index] == "object") {
                    form.add_files[index] = files[index];
                }
            }
            validateFiles(form);
        } else {
            for (let i = 0; i < items.length; i++) {
                if (items[i].type === "text/plain") {
                    items[i].getAsString((string) => {
                        form.message += string;
                    });
                }
            }
        }
    },
    inputData(event, form) {
        let files = event.target.files;
        for(let index in files) {
            if(typeof files[index] == "object") {
                form.add_files[files[index].name] = files[index];
            }
        }
        validateFiles(form);
    },
    uploadFiles(form, object, attachmentable_id = null, attachmentable_type = null) {
        let r = new Resumable({
            target: `/upload/files/${attachmentable_id}/${attachmentable_type}`,
            query: { _token: document.querySelector('meta[name="csrf-token"]').content },
            fileType: [],
            chunkSize: 2 * 1024 * 1024, // default is 1*1024*1024, this should be less than your maximum limit in php.ini
            simultaneousUploads: 1,
            headers: {
                'Accept': 'application/json'
            },
            testChunks: false,
            throttleProgressCallbacks: 1,
        });
    
        for(let key in form.add_files) {
            r.addFile(form.add_files[key]);
        }
    
        r.on('fileAdded', function(file, event){
            if (file.file.type.includes("image")) {
                object.images.push({ stub: true, identifier: file.file.uniqueIdentifier, percentage: '0%' });
            } else if (file.file.type.includes("video")) {
                object.videos.push({ stub: true, identifier: file.file.uniqueIdentifier, percentage: '0%' });
            } else {
                object.files.push({ stub: true, identifier: file.file.uniqueIdentifier, percentage: '0%' });
            }
    
            r.upload() // to actually start uploading
        });
    
        r.on('fileProgress', function(file, event){
            let value = Math.floor(file.progress() * 100);
    
            if (file.file.type.includes("image")) {
                updateProgress(object, file, 'images');
            } else if (file.file.type.includes("video")) {
                updateProgress(object, file, 'videos');
            } else {
                updateProgress(object, file, 'files');
            }
    
            function updateProgress(object, file, type) {
                for(let i = 0; i < object[type].length; i++) {
                    if(object[type][i].stub) {
                        if(object[type][i].identifier == file.file.uniqueIdentifier) {
                            object[type][i].percentage = `${value}%`;
                        }
                    }
                }
            }
        });
    
        r.on('fileSuccess', function(file, response){
            response = JSON.parse(response);
            
            if (response.mime_type.includes("image")) {
                replaceStub(object, file, 'images');
            } else if (response.mime_type.includes("video")) {
                replaceStub(object, file, 'videos');
            } else {
                replaceStub(object, file, 'files');
            }

            form.add_files = {};
    
            function replaceStub(object, file, type) {
                for(let i = 0; i < object[type].length; i++) {
                    if(object[type][i].stub) {
                        if(object[type][i].identifier == file.file.uniqueIdentifier) {
                            object[type].splice(i, 1, response.data);
                        }
                    }
                }
            }
        });
    
        r.on('fileError', function(file, response) {
            let mimes = file.file.type;
            let type = mimes.split('/')[0];
            form.errors.message = response.message;
            
            if (type === 'image') {
                removeStub(object, file, 'images');
            } else if (type === 'video') {
                removeStub(object, file, 'videos');
            } else {
                removeStub(object, file, 'files');
            }
            
            function removeStub(object, file, type) {
                for(let i = 0; i < object[type].length; i++) {
                    if(object[type][i].stub) {
                        if(object[type][i].identifier == file.file.uniqueIdentifier) {
                            object[type].splice(i, 1);
                        }
                    }
                }
            }
        });
        
        this.clearErrors(form);
    },
    setComparisonData(value, form) {
        value.message = form.message;
        value.add_files = JSON.stringify(form.add_files);
        value.add_links = JSON.stringify(form.add_links);
        value.delete_attachments = JSON.stringify(form.delete_attachments);
    },
    isEqual(before, after) {
        for(let index in before) {
            if(before[index] !== after[index]) {
                return false;
            }
        }
    
        return true;
    },
    getRandomString(length = 5) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!"№;%:?*)(+=_/|><`}{][-';
        const charactersLength = characters.length;
    
        for(let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
    
        return result;
    },
    throttle(callee, timeout) {
        let timer = null;

        return function perform(...args) {
            if (timer) return

            timer = setTimeout(() => {
                callee(...args);

                clearTimeout(timer);
                timer = null;
            }, timeout);
        }
    },
    showViewBox(store, items, index, owner) {
        store.$patch({
            index: index,
            items: items,
        });

        store.$patch({
            id: items[index].id,
            type: items[index].type,
            src: items[index].src,
            user: owner,
            comments: items[index].comments,
            likes: items[index].likes,
            created_at: items[index].created_at,
            embedded: items[index].embedded,

            show: true
        });
        
        hideScroll();
    },
    closeViewBox(store) {
        store.$reset();
        showScroll();
    },
    collapseTextarea(element, form = null, flag = false) {
        let text = element.value.trim();

        if(text.length === 0 || flag === true) {
            element.value = '';

            if(form) {
                form.message = '';
            }
        }

        this.textAreaAdjust(element);
    }
}

export default library;

async function validateFiles(form) {
    const data = new FormData();
    for(let index in form.add_files) {
        data.append('file' + index, form.add_files[index]);
    }

    let config = {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    };

    if(form.automatic_upload) {
        config.headers['gallery'] = 'true';
    }

    await axios.post(route('files.validate'), data, config)
    .then(result => {
        for(let index in form.add_files) {
            if(result.data.declined_keys.includes('file' + index)) {
                delete form.add_files[index];
                form.errors.message = result.data.errors[0];
                library.clearErrors(form);
            }
        }
    }).catch(error => {
        if (error.response.status === 403) {
            window.location.href = route('verification.notice');
        }
    });

    if(form.automatic_upload) {
        library.uploadFiles(form, form);
    } else {
        insertFiles(form);
    }
}

async function getFile(form, index) {
    return new Promise((resolve, reject) => {
        var reader = new FileReader();
        reader.onload = (event) => {
            let file = createFileObject(event.target.result, form.add_files[index]);
            resolve(file);
        }
        reader.readAsDataURL(form.add_files[index]);
    });
}

async function insertFiles(form) {
    for(let index in form.add_files) {
        let file = await getFile(form, index);
        
        if(file.type == 'image') {
            if(!checkForDuplicates(form.images, file)) {
                form.images.push(file);
            }
        } else if(file.type == 'video') {
            if(!checkForDuplicates(form.videos, file)) {
                form.videos.push(file);
            }
        } else {
            if(!checkForDuplicates(form.files, file)) {
                form.files.push(file);
            }
        }
    }
}

function checkForDuplicates(arr, file) {
    let flag = false;

    arr.forEach((elem, index, arr) => {
        if(elem.id === file.id) {
            flag = true;
        }
    });

    return flag;
}
function url_parser(url, obj) {
    let youtube = youtube_parser(url, obj);
    if(youtube) {return;}
    let vk = vk_parser(url, obj);
    if(vk) {return;}
    let twitch = twitch_parser(url, obj);
    if(twitch) {return;}
}
function youtube_parser(url, obj) {
    // http://www.youtube.com/watch?v=0zM3nApSvMg&feature=feedrec_grec_index
    // http://www.youtube.com/user/IngridMichaelsonVEVO#p/a/u/1/QdK8U-VIH_o
    // http://www.youtube.com/v/0zM3nApSvMg?fs=1&amp;hl=en_US&amp;rel=0
    // http://www.youtube.com/watch?v=0zM3nApSvMg#t=0m10s
    // http://www.youtube.com/embed/0zM3nApSvMg?rel=0
    // http://www.youtube.com/watch?v=0zM3nApSvMg
    // http://youtu.be/0zM3nApSvMg

    let regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
    let match = url.match(regExp);

    if(match&&match[7].length == 11) {
        obj.type = 'video';
        obj.embedded = true;
        obj.videoId = match[7];
        obj.src = "https://www.youtube.com/embed/" + obj.videoId;

        return true;
    }

    return false;
}
function vk_parser(url, obj) {
    // https://vk.com/video-55155418_456239723
    // https://vk.com/video?z=video-55155418_456239723%2Fpl_cat_trends

    let regExp = /https?:\/\/vk\.com\/video.*(-[0-9]+)_([0-9]+).*/;
    let match = url.match(regExp);

    if(match && match[1].length > 0 && match[2].length > 0) {
        obj.type = 'video';
        obj.embedded = true;
        obj.videoOid = match[1];
        obj.videoId = match[2];
        obj.src = "https://vk.com/video_ext.php?oid=" + obj.videoOid + "&id=" + obj.videoId + "&hd=4";

        return true;
    }

    return false;
}
function twitch_parser(url, obj) {
    // https://www.twitch.tv/skrolya?tt_content=channel_name&tt_medium=embed
    // https://www.twitch.tv/skrolya
    // https://www.twitch.tv/videos/506141723?tt_content=channel_name&tt_medium=embed
    // https://www.twitch.tv/videos/506141723
    // https://clips.twitch.tv/SucculentHardRutabagaMVGame?tt_content=channel_name&tt_medium=embed
    // https://clips.twitch.tv/SucculentHardRutabagaMVGame

    let videoRegExp = /https?:\/\/www\.twitch\.tv\/videos\/([0-9]+)/;
    let clipRegExp = /https?:\/\/clips\.twitch\.tv\/([a-zA-Z]+)/;
    let streamRegExp = /https?:\/\/www\.twitch\.tv\/([a-zA-Z0-9]+)/;

    let matchVideo = url.match(videoRegExp);
    let matchClip = url.match(clipRegExp);
    let matchStream = url.match(streamRegExp);
    let domain = window.location.hostname; // or .host

    if(matchVideo) {
        obj.type = 'video';
        obj.embedded = true;
        obj.videoId = matchVideo[1];
        obj.src = "https://player.twitch.tv/?video=v" + obj.videoId + "&parent=" + domain + "&autoplay=false";
        
        return true;
    } else if(matchClip) {
        obj.type = 'video';
        obj.embedded = true;
        obj.clipSlug = matchClip[1];
        obj.src = "https://clips.twitch.tv/embed?clip=" + obj.clipSlug + "&parent=" + domain;
        
        return true;
    } else if(matchStream) {
        obj.type = 'video';
        obj.embedded = true;
        obj.channelName = matchStream[1];
        obj.src = "https://player.twitch.tv/?channel=" + obj.channelName + "&parent=" + domain + "&muted=true";

        return true;
    }

    return false;
}
function getLinkData(links, form) {
    let data = {
        links: links
    }
    
    axios.post(route('links.data'), data)
    .then(result => {
        if(result.data) {
            for(let obj of result.data) {
                switch(obj.type) {
                    case 'text':
                        form.links.push(obj);
                    break;

                    case 'image':
                        form.images.push(obj);
                    break;

                    case 'video':
                        form.videos.push(obj);
                    break;

                    default:
                        form.files.push(obj);
                    break;
                }

                form.add_links.push(obj);
            }
        }
    });
}
function createFileObject(src, item) {
    let obj = {};
    let urlRegex = /([a-z]+)\/([a-zA-Z0-9+.-]+)/;
    let match = item.type.match(urlRegex);

    // obj.id = library.getRandomString(20);
    obj.id = item.name;
    obj.src = src;

    if(match) {
        obj.type = match[1];
    } else {
        obj.type = 'application';
    }
    
    obj.size = item.size;
    obj.original_title = item.name;

    return obj;
}
function createLinkObject(form, string) {
    let urlRegex = /(https?:\/\/[-\w.]+\.[a-z]{2,3})([-.A-Za-z0-9/]+)*([^\s,]*)/g;
    let matches = string.matchAll(urlRegex);
    let links = [];

    if(matches) {
        for(let match of matches) {
            let obj = {};

            obj.id = match[0];
            obj.src = match[0];
            obj.type = null;
            obj.spec = null;
            obj.domain = match[1];
            obj.uri = match[2] + match[3];
            obj.file = false;

            url_parser(string, obj);

            links.push(obj);
        }

        getLinkData(links, form);
    }
}
function hideScroll() {
    let html = document.querySelector('html');
    if(html.scrollHeight > html.clientHeight) {
        html.classList.add('overflow-hidden');
    }
}
function showScroll() {
    let html = document.querySelector('html');
    html.classList.remove('overflow-hidden');
}