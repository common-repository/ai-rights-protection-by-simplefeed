SimpleFeedBotsProtection = {
    $: undefined,
    loadHistory : (bot,limit,group_by_bot,onSuccess) => {
        let params = {
            action: 'wp_simplefeed_bots_protection_history',
            nonce: MyAjax.nonce
        }
        if (bot!=undefined) {
            params['bot'] = bot
        }
        if (limit!=undefined) {
            params['limit'] = limit
        }
        if (group_by_bot!=undefined) {
            params['group_by_bot'] = true;
        }
        SimpleFeedBotsProtection.$.post(MyAjax.ajaxurl, params, response => {
            onSuccess && onSuccess(response.data.list)
        })
    },
    loadLog: onSuccess => {
        SimpleFeedBotsProtection.$.post(MyAjax.ajaxurl, {
            action: 'wp_simplefeed_bots_protection_log',
            nonce: MyAjax.nonce
        }, response => {
            onSuccess && onSuccess(response.data.list)
        })
    },
    setPagination: (paginationQuery,rowsQuery,count,pageSize)=>{
        let pagination = document.querySelector(paginationQuery)
        pagination.innerHTML = ``
        //pagination.innerHTML = pagination.innerHTML + `<a href="javascript://" class="prev">&laquo;</a>`
        let current = pagination.getAttribute('current')
        for (let i = 1; i <= Math.ceil(count / pageSize); i++) {                
            pagination.innerHTML = pagination.innerHTML + `<a href="javascript://" class="page-number `+(current==i?'active':'')+`" data-page="`+i+`">`+i+`</a>`
        }
        //pagination.innerHTML = pagination.innerHTML + `<a href="#" class="next">&raquo;</a>`
        const rows = document.querySelectorAll(rowsQuery);
        const paginationLinks = document.querySelectorAll(""+paginationQuery+' a.page-number');
        function showPage(page) {
            const start = (page - 1) * pageSize;
            const end = start + pageSize;
            rows.forEach((row, index) => {
                if (index >= start && index < end) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });        
            paginationLinks.forEach(link => {
                link.classList.remove('active');
                if (parseInt(link.dataset.page) === page) {
                    link.classList.add('active');
                }
            });
            pagination.setAttribute('current',page)
        }            
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const page = parseInt(this.dataset.page);
                showPage(page);
            });
        });
        showPage(1);

    },
    renderHistory: bot => {
        let refresh = () =>{
            let json = JSON.parse(document.getElementById('simplefeed_bots_protection_settings_bots').value)
            SimpleFeedBotsProtection.loadHistory(bot,undefined,undefined,history=>{
                document.querySelector('[edition][key='+bot+'] table[right] b[for]').innerHTML='' + (bot || 'all');
                let table = document.querySelector('[edition][key='+bot+'] table[right] table');
                let headers = `
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Start</th>
                        <th>Last</th>
                        <th title='Requests'>Requests</th>                    
                        <th title='Success'>Allowed</th>
                        <th title='Denied'>Blocked</th>
                        <th action>Action</th>
                    </tr>
                </thead>`
                table.innerHTML = `
                ${history.length>0 ? headers: ''}
                ${history.length>0 ? '<tbody>': ''}
                `+history.reduce((acc,h)=>{            
                    let hh = (h.history==null ? '' : h.history).split('~').map(x=>x.split('='))
                    let firstUserAgent = hh.length>0 && hh[0].length>1 ? hh[0][1] : '';
                    //let last = hh.length>0 && hh[hh.length-1].length>0 ? hh[hh.length-1][0] : '';
                    let last = hh.length>0 ? hh[0][0] : '';
                    let lastUserAgent = hh.length>0 ? hh[0][1]: '';
                    let link = `<a href='${MyAjax.home_url}/?robots=1&simplefeed_token=${h.token}' target='_blank' style='text-decoration: none;'>${h.ip}</a>`;
                    let enable = h.enable=='1'
                    let botEnablePartial = (json[bot].sitemap.items.types || []).length>0 || (json[bot].sitemap.items.tags || []).length>0 || (json[bot].sitemap.items.categories || []).length>0;
                    let style = `color: ${enable==false?'darkgreen': (botEnablePartial?'#c16c04':'darkred')} !important'`                    
                    let enableStyle = enable==true ? ( botEnablePartial ? 'color:#c56f08': 'color:red' ) : 'color:green';
                    let enableText  = enable==true ? ( botEnablePartial ? 'Allowed (Partial)': 'Blocked' ) : 'Allowed';
                    let sliderStyle = enable==true ? ( botEnablePartial ? 'background-color:#f39b31': 'background-color:#ff000082' ) : 'background-color:#047e045c';
                    return acc+`
                        <tr>                        
                            <td style='${style}'>${link}</td>
                            <td style='${style}' title="${firstUserAgent}">${h.time}</td>
                            <td style='${style}' title="${lastUserAgent}">${last}</td>
                            <td style='${style};text-align:center'>${h.robots_txt_requests}</td>
                            <td style='${style};text-align:center'>${h.robots_txt_requests_200}</td>
                            <td style='${style};text-align:center'>${h.robots_txt_requests_403}</td>
                            <td style=''>
                                <label title='`+enableText+`' style='`+enableStyle+`'>
                                    <input type="checkbox" token="${h.token}" class="simplefeed_bots_protection_history_action_enable" `+(enable?'checked=""':'')+`>
                                    <span class="simplefeed_bots_protection_history_action_enable_slider" style='`+sliderStyle+`'></span>
                                    <b style='text-wrap: nowrap;'>`+enableText+`</b>
                                </label>
                            </td>
                        </tr>
                    `;
                },'')
                +(history.length>0 ? '</tbody>':'')
                if (history.length==0) {
                    document.querySelector('[edition][key='+bot+'] table[right] [no_bot]').innerHTML = `No bots detected yet. Please wait till this particular robot reach your side. `;
                } else {
                    document.querySelector('[edition][key='+bot+'] table[right] [no_bot]').innerHTML = ``;
                }
                document.querySelectorAll('[edition][key='+bot+'] .simplefeed_bots_protection_history_action_enable').forEach( input => {
                    input.addEventListener('change', event => {
                        event.preventDefault()
                        SimpleFeedBotsProtection.$.post(MyAjax.ajaxurl, {
                            action: event.target.checked ? 'wp_simplefeed_bots_protection_history_action_enable' : 'wp_simplefeed_bots_protection_history_action_disable',
                            token: event.target.getAttribute('token'),
                            nonce: MyAjax.nonce
                        }, response => {
                            refresh()
                        })
                    })
                })
                if (history.length>5) {
                    SimpleFeedBotsProtection.setPagination(
                        '[edition][key='+bot+'] table[right] [pagination]',
                        '[edition][key='+bot+'] table[right] table tbody tr',
                        history.length,
                        5
                    )
                }
            })
            SimpleFeedBotsProtection.loadLog( log => {
                SimpleFeedBotsProtection.log = log
            })
        }
        refresh()
    },
    adjustEditorHeight: editor => {
        var newHeight = editor.session.getScreenLength() * editor.renderer.lineHeight + editor.renderer.scrollBar.getWidth()
        editor.container.style.height = newHeight + "px"
        editor.resize()
    },
    renderSettingsAsJson: () => {
        var editor = ace.edit("simplefeed_bots_protection_settings_bots_editor")
        //editor.session.setMode("ace/mode/json")
        editor.setOptions({
            maxLines: Infinity,
            wrap: true
        })
        let element = document.getElementById("simplefeed_bots_protection_settings_bots")
        let jsonText = element.value
        try {
            var jsonObj = JSON.parse(jsonText)
            var formattedJson = JSON.stringify(jsonObj, null, 4);
            editor.setValue(formattedJson, -1);
        } catch (e) {
            editor.setValue(jsonText, -1);
        }
        editor.session.on('change', () => {                    
            let el = document.getElementById('simplefeed_bots_protection_settings_bots')
            el.value = editor.getValue()
            el.innerHTML = editor.getValue()  
            SimpleFeedBotsProtection.adjustEditorHeight(editor)
            setTimeout(()=>{
                SimpleFeedBotsProtection.renderSettingsAsTable()
            },200)            
        })
        SimpleFeedBotsProtection.adjustEditorHeight(editor)
    },
    /*bindClickDeleteAll: ()=>{
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[action='deleteAll']`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            e.preventDefault()
            if (confirm("Are you sure to delete All rules except first ?")) {
                let el = document.getElementById(id_prefix)
                let j  = JSON.parse(el.value)
                Object.keys(j).forEach(key=>{
                    if (key!=Object.keys(j)[0]) {
                        delete j[key]
                    }
                })
                el.value = JSON.stringify(j, null, 4)
                SimpleFeedBotsProtection.renderSettingsAsJson()
            }                        
        })
    },*/
    /*bindClickHistoryAll: ()=>{
        let query = `[action='historyAll']`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            e.preventDefault()
            SimpleFeedBotsProtection.renderHistory()
            SimpleFeedBotsProtection.$('#simplefeed_bots_protection_history_frame').fadeIn()
            SimpleFeedBotsProtection.$('#simplefeed_bots_protection_history_frame-close').on('click', () => {
                SimpleFeedBotsProtection.$('#simplefeed_bots_protection_history_frame').fadeOut();
            })
            SimpleFeedBotsProtection.$(window).on('click', event =>{
                if (SimpleFeedBotsProtection.$(event.target).is('#simplefeed_bots_protection_history_frame')) {
                    SimpleFeedBotsProtection.$('#simplefeed_bots_protection_history_frame').fadeOut()
                }
            });
        })
    },*/
    bindChangeRuleKey: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='key']`;
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            let el = document.getElementById(id_prefix)
            let j = JSON.parse(el.value)
            let oldKey = e.target.getAttribute('key')
            let newKey = e.target.value
            if (j.hasOwnProperty(oldKey)) {
                j[newKey] = j[oldKey];
                delete j[oldKey];
            }
            el.value = JSON.stringify(j, null, 4)
            //SimpleFeedBotsProtection.renderSettingsAsJson()
        })
    },
    bindChangeRuleDenyText: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='deny_text']`;
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            let el = document.getElementById(id_prefix)
            let j = JSON.parse(el.value);
            let v = document.querySelector(query).value
            j[key]['deny_text'] = v
            el.value = JSON.stringify(j, null, 4)
            //SimpleFeedBotsProtection.renderSettingsAsJson()
        })
    },
    bindChangeRuleUserAgentContains: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='user_agent_contains']`;
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            let el = document.getElementById(id_prefix)
            let j = JSON.parse(el.value);
            let v = document.querySelector(query).value
            j[key]['user_agent_contains'] = v.split(',').map(x=>x.trim())
            el.value = JSON.stringify(j, null, 4)
            //SimpleFeedBotsProtection.renderSettingsAsJson()
        })
    },
    bindChangeRuleNumberPosts: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='numberposts']`;
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            let el = document.getElementById(id_prefix)
            let j = JSON.parse(el.value);
            let v = document.querySelector(query).value
            j[key]['sitemap']['items']['numberposts'] = parseInt(v)
            el.value = JSON.stringify(j, null, 4)
            //SimpleFeedBotsProtection.renderSettingsAsJson()
        })
    },
    bindChangeRuleTypes: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='types']`
        SimpleFeedBotsProtection.$(query).each((i,x)=>{
            x.addEventListener('change', e => {
                let el = document.getElementById(id_prefix)
                let j = JSON.parse(el.value);
                let v = []
                SimpleFeedBotsProtection.$(query).each((i,x)=>{
                    if (x.checked==true) {
                        v.push(x.value)
                    }
                })
                j[key]['sitemap']['items']['types'] = v
                el.value = JSON.stringify(j, null, 4)
            })
        })
    },
    bindChangeRuleCategories: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='categories']`
        SimpleFeedBotsProtection.$(query).each((i,x)=>{
            x.addEventListener('change', e => {
                let el = document.getElementById(id_prefix)
                let j = JSON.parse(el.value);
                let v = []
                SimpleFeedBotsProtection.$(query).each((i,x)=>{
                    if (x.checked==true) {
                        v.push(parseInt(x.value))
                    }
                })
                j[key]['sitemap']['items']['categories'] = v
                el.value = JSON.stringify(j, null, 4)
            })
        })
    },
    bindChangeRuleTags: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='tags']`;
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            let el = document.getElementById(id_prefix)
            let j = JSON.parse(el.value);
            let v = document.querySelector(query).value
            j[key]['sitemap']['items']['tags'] = ((v || '').split(',') || []).map(x=>x.trim()).filter(x=>x!='').map(x=>(Object.values(MyAjax.tags || {}).find(y=>y.slug==x) || {}).term_id).filter(x=>x!=undefined)
            el.value = JSON.stringify(j, null, 4)
        })
        /*
        SimpleFeedBotsProtection.$(query).each((i,x)=>{
            x.addEventListener('change', e => {
                let el = document.getElementById(id_prefix)
                let j = JSON.parse(el.value);
                let v = []
                console.log()
                SimpleFeedBotsProtection.$(query).each((i,x)=>{
                    if (x.checked==true) {
                        v.push(x.value)
                    }
                })
                j[key]['sitemap']['items']['tags'] = v
                el.value = JSON.stringify(j, null, 4)
            })
        })*/
    },
    bindChangeEnable: () => {
        let query = `[name='simplefeed_bots_protection_settings_enabled']`
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            document.querySelector("form").submit.click()
        })
    },
    bindChangeEnableBackup: () => {
        let query = `[name='simplefeed_bots_protection_settings_enabled_backup']`
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            document.querySelector("form").submit.click()
        })
    },
    bindClickNeedPremium: ()=>{ 
        let query = `.need_premium`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            window.open("https://www.simplefeed.com/solutions/ai-protection/", "_blank");
        })
    },
    bindClickAdditionalSave: ()=>{
        let query = `.additional input[type=button]`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            document.querySelector("form").submit.click()
        })
    },
    bindChangeRuleEnable: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='enable']`;
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            //e.preventDefault()
            let el = document.getElementById(id_prefix)
            let j = JSON.parse(el.value);
            let v = document.querySelector(query).checked
            j[key]['enable'] = v
            if (v==false) {
                j[key].sitemap.items.tags = j[key].sitemap.items.types = j[key].sitemap.items.categories = []
            }
            el.value = JSON.stringify(j, null, 4)
            SimpleFeedBotsProtection.$.post(MyAjax.ajaxurl, {
                action: v ? 'wp_simplefeed_bots_protection_history_action_enable' : 'wp_simplefeed_bots_protection_history_action_disable',
                bot: key,
                nonce: MyAjax.nonce
            }, response => {}).always(function() {
                document.querySelector("form").submit.click()
            })

        })
    },
    bindClickRuleAdd: () => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[action='add']`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            e.preventDefault()
            let el = document.getElementById(id_prefix)
            let j  = JSON.parse(el.value)
            if (j.hasOwnProperty('new_bot')==false) {
                j['new_bot'] = JSON.parse(JSON.stringify(j[Object.keys(j)[Object.keys(j).length-1]]))
                j['new_bot']['user_agent_contains'] = 'New Bot User Agent'.split(',').map(x=>x.trim())
            }
            el.value = JSON.stringify(j, null, 4)
            document.querySelector("form").submit.click()
        })
    },
    /*bindClickRuleDelete: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][action='delete']`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            e.preventDefault()
            if (confirm("Are you sure to delete the rule for "+key+" ?")) {
                let el = document.getElementById(id_prefix)
                let j  = JSON.parse(el.value)
                if (j.hasOwnProperty(key)) {
                    delete j[key]
                }
                el.value = JSON.stringify(j, null, 4)
                SimpleFeedBotsProtection.renderSettingsAsJson()
            }                        
        })
    },*/
    bindClickRuleEdit: key => {
        let query = `[key='`+key+`'][action='edit']`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            if (SimpleFeedBotsProtection.$(e.target).parents('td:first').find('[path=enable]').length!=1) {
                e.preventDefault()
            }
            let rowEdition = SimpleFeedBotsProtection.$(`tr[edition][key='`+key+`']`)
            if (rowEdition.length>0 && rowEdition[0].checkVisibility()==false) {
                SimpleFeedBotsProtection.$(`tr[viewing][key='`+key+`'] [open]`)[0].setAttribute('open',true)
                rowEdition.show()
                SimpleFeedBotsProtection.renderHistory(key)
            } else {
                SimpleFeedBotsProtection.$(`tr[viewing][key='`+key+`'] [open]`)[0].setAttribute('open',false)
                rowEdition.hide()
            }
        })
        query = `a[key='`+key+`'][action='history']`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            e.preventDefault()
            let row = SimpleFeedBotsProtection.$(`tr[viewing][key='`+key+`']`)
            let rowEdition = SimpleFeedBotsProtection.$(`tr[edition][key='`+key+`']`)
            if (rowEdition.length>0 && rowEdition[0].checkVisibility()==false) {
                rowEdition.show()
                SimpleFeedBotsProtection.renderHistory(key)
            } else {
                rowEdition.hide()
            }
        })
        query = `tr[edition][key='`+key+`'] [delete_it]`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            e.preventDefault()
            if (confirm("Are you sure to delete the rule for "+key+" ?")) {
                let id_prefix = 'simplefeed_bots_protection_settings_bots'
                let el = document.getElementById(id_prefix)
                let j  = JSON.parse(el.value)
                if (j.hasOwnProperty(key)) {
                    delete j[key]
                }
                el.value = JSON.stringify(j, null, 4)
                document.querySelector("form").submit.click()
            }
        })
        query = `tr[edition][key='`+key+`'] [submit_it]`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            e.preventDefault()
            document.querySelector("form").submit.click()
        })
        query = `tr[edition][key='`+key+`'] [close_it]`;
        SimpleFeedBotsProtection.$(query).off('click').on('click', e => {
            e.preventDefault()
            let rowEdition = SimpleFeedBotsProtection.$(`tr[edition][key='`+key+`']`)
            rowEdition.hide()
        })
    },
    /*bindChangeRuleSitemapEnable: key => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let query = `[key='`+key+`'][path='sitemap_enable']`;
        SimpleFeedBotsProtection.$(query).off('change').on('change', e => {
            let el = document.getElementById(id_prefix)
            let j = JSON.parse(el.value);
            let v = document.querySelector(query).checked
            j[key]['sitemap']['sm_enable'] = v
            el.value = JSON.stringify(j, null, 4)
            //SimpleFeedBotsProtection.renderSettingsAsJson()
        })
    },*/
    /*bindClickRuleHistory: key => {
        let onClick = e => {
            e.preventDefault()
            SimpleFeedBotsProtection.renderHistory(key)
            SimpleFeedBotsProtection.$('#simplefeed_bots_protection_history_frame').fadeIn()
            SimpleFeedBotsProtection.$('#simplefeed_bots_protection_history_frame-close').on('click', () => {
                SimpleFeedBotsProtection.$('#simplefeed_bots_protection_history_frame').fadeOut();
            })
            SimpleFeedBotsProtection.$(window).on('click', event =>{
                if (SimpleFeedBotsProtection.$(event.target).is('#simplefeed_bots_protection_history_frame')) {
                    SimpleFeedBotsProtection.$('#simplefeed_bots_protection_history_frame').fadeOut()
                }
            });
        }
        let query = `td a > [key='`+key+`'][action='history']`;
        document.querySelector(query)!=null && document.querySelector(query).addEventListener('click', onClick)
        query = `td .dropdown-menu > [key='`+key+`'][action='history']`;
        document.querySelector(query)!=null && document.querySelector(query).addEventListener('click', onClick)
    },*/
    renderSettingsAsTable: () => {
        let id_prefix = 'simplefeed_bots_protection_settings_bots'
        let table = document.getElementById(id_prefix+'_table')
        let pagination = document.getElementById(id_prefix+'_pagination')
        try {
            let jsonElement = document.getElementById(id_prefix)
            let json = JSON.parse(jsonElement.value)
            var html = ''
                html = html + `<thead>`
                html = html + `<tr>`                
                html = html + `     <th class='head_select'><!--<input type='checkbox' path='select'/>--></th>`
                html = html + `     <th class='head_name'>Bot Name</th>`
                html = html + `     <th class='head_count'>Requests</th>`
                html = html + `     <th class='head_last_ip'>IP address</th>`
                html = html + `     <th class='head_last_date'>Last Crawl Date</th>`
                html = html + `     <th class='head_enable'>Action</th>`
                html = html + `     <th class='head_menu'></th>`
                html = html + `</tr>`
                html = html + `</thead>`
            var id = 1;
            Object.keys(json).forEach(x=>{
                let pr = id_prefix+'_'+(x.toLowerCase().replaceAll(' ','_'))
                let botName = x
                let botEnable = json[x].enable
                let botEnablePartial = (json[x].sitemap.items.types || []).length>0 || (json[x].sitemap.items.tags || []).length>0 || (json[x].sitemap.items.categories || []).length>0;
                let botDenyText = json[x].deny_text
                let botUserAgentContains = json[x].user_agent_contains.join(', ')
                let botNumberPosts = json[x].sitemap.items.numberposts
                let botTypes = json[x].sitemap.items.types
                let botCategories = json[x].sitemap.items.categories // ids
                let botTags = (json[x].sitemap.items.tags || []).filter(x=>x!=null).map(z=>(Object.values(MyAjax.tags || {}).find(y=>y.term_id==z) || {}).slug).filter(z=>z!=undefined);
                html = html + `<tr key='`+x+`' viewing action='edit'>`                
                html = html + `     <td>`
                html = html + `         <!-- <input type='checkbox' key='`+x+`' path='select'/> -->`
                html = html + `         `
                html = html + `     </td>`
                let botNameTitle = botName, botNameStyle = '';
                if (botNameTitle=='new_bot') {
                    botNameTitle = '* Please config new rule here'
                    botNameStyle = 'color: darkorange;'
                }
                html = html + `     <td><b key='`+x+`' path='key' style='`+botNameStyle+`'>`+botNameTitle+`</b></td>`                
                
                html = html + `     <td><div key='`+x+`' path='history_count'></div></td>`
                html = html + `     <td><div key='`+x+`' path='history_last_ip'></div></td>`
                html = html + `     <td><div key='`+x+`' path='history_last_date'></div></td>`
                let enableStyle = botEnable==true ? ( botEnablePartial ? 'color:#c56f08': 'color:red' ) : 'color:green';
                let enableText  = botEnable==true ? ( botEnablePartial ? 'Allowed (Partial)': 'Blocked' ) : 'Allowed';
                let sliderStyle = botEnable==true ? ( botEnablePartial ? 'background-color:#f39b31': 'background-color:#ff000082' ) : 'background-color:#047e045c';
                html = html + `     <td>
                                        <label key='`+x+`' path='enable_label' style='`+enableStyle+`'>
                                            <input type='checkbox' key='`+x+`' path='enable' `+(json[x].enable?'checked':'')+`/>
                                            <span key='`+x+`' path='enable_slider' class='simplefeed_bots_protection_slider' style='`+sliderStyle+`'></span>
                                            <b key='`+x+`' path='enable_text' style='text-wrap: nowrap;'>`+enableText+`</b>
                                        </label>
                                    </td>`
                html = html + `     <td>`
                html = html + `         <a key='`+x+`' href="javascript://" class="dropdown-item">
                                            <span class="icon" open="false"></span>
                                        </a>
                                        
                                        <!--<div class="dropdown">
                                                <div class="dropdown-toggle">⋮</div>
                                                <div class="dropdown-menu">
                                                    <a key='`+x+`' action='history' href="javascript://" class="dropdown-item">
                                                        <span class="icon">📜</span> Show requests
                                                    </a>
                                                </div>
                                        </div>-->
                                        `
                html = html + `     </td>`
                html = html + `</tr>`                
                html = html + `<tr key='`+x+`' edition style='display:none'>`
                html = html + `     <td></td>`
                html = html + `     <td colspan=6>`
                html = html + `         <div>`
                html = html + `         <table left class='`+(MyAjax.P!=true?'need_premium':'')+`'>`
                html = html + `             <tr>`
                html = html + `                 <th colspan=2><h2>Settings</h2></th>`
                html = html + `             </tr>`
                html = html + `             <tr>`
                html = html + `                 <th>Bot name</th>`
                html = html + `                 <td>
                                                    <input key='`+x+`' path='key' value='`+botName+`' style='' />
                                                </td>`
                html = html + `             </tr>`
                html = html + `             <tr>`
                html = html + `                 <th>Deny text</th>`
                html = html + `                 <td>
                                                    <textarea rows='3' key='`+x+`' path='deny_text'>`+botDenyText+`</textarea>
                                                </td>`
                html = html + `             </tr>`
                html = html + `             <tr>`
                html = html + `                 <th>User Agent</th>`
                html = html + `                 <td>
                                                    <input key='`+x+`' path='user_agent_contains' value='`+botUserAgentContains+`'/>
                                                </td>`
                html = html + `             </tr>`
                html = html + `             <tr>`
                html = html + `                 <td colspan=2>
                                                    <h2 style='padding: 0.3rem 0rem;' title='Exception for blocking'>
                                                        Exception for blocking
                                                    </h2>
                                                </th>`
                html = html + `             </tr>`
                html = html + `             <tr>`
                html = html + `                 <th style="padding: 5px 0px;">Post count</th>`
                html = html + `                 <td style="padding: 5px 0px;">
                                                    <input type='number' key='`+x+`' path='numberposts' value='`+botNumberPosts+`'/>
                                                </td>`
                html = html + `             </tr>`
                html = html + `             <tr>`
                html = html + `                 <th style="vertical-align: top; padding: 5px 0px;">Post types</th>`
                html = html + `                 <td types style="padding: 5px 0px;">
                                                    `+(Object.values(MyAjax.types || {})).map(y => `<label style='display: inline-flex; padding: 0.0rem 0.3rem 0.3rem 0.0rem'><input type='checkbox' key='`+x+`' path='types' value='`+y+`' `+((botTypes || []).indexOf(y)>-1?'checked="true"':'')+`style='width: 1.1rem;margin-top: 0.1rem !important;' /><span>`+y+`</span></label>`).join('')+`
                                                </td>`
                html = html + `             </tr>`
                html = html + `             <tr>`
                html = html + `                 <th style="vertical-align: top; padding: 5px 0px;">Post categories</th>`
                html = html + `                 <td categories style="padding: 5px 0px;">
                                                    `+(Object.values(MyAjax.categories || {})).map(y => `<label style='display: inline-flex; padding: 0.0rem 0.3rem 0.3rem 0.0rem'><input type='checkbox' key='`+x+`' path='categories' value='`+y.term_id+`' `+((botCategories || []).indexOf(y.term_id)>-1?'checked="true"':'')+`style='width: 1.1rem;margin-top: 0.1rem !important;' /><span>`+y.name+`</span></label>`).join('')+`
                                                </td>`
                html = html + `             </tr>`
                const tagsTop = MyAjax.tags.sort((a, b) => b.count - a.count).slice(0, 4).map(x=>x.slug)
                html = html + `             <tr>`
                html = html + `                 <th  style="vertical-align: top; padding: 5px 0px;" title="Coma separated list of tags">Post tags</th>`
                html = html + `                 <td tags style="padding: 5px 0px;">
                                                    <textarea rows='2' key='`+x+`' path='tags'>`+botTags.join(',')+`</textarea>
                                                    <span style='font-size: small;padding-top: 0.2rem;padding-left: 0.5rem;display: block;'>Most used: `+tagsTop.map(y=> {
                                                        return `<a href="javascript:void(0);" onclick="document.querySelector('textarea[key=`+x+`][path=tags]').value += ', `+y+`';SimpleFeedBotsProtection.$('[key=`+x+`][path=tags]')[0].dispatchEvent(new Event('change'))" style="text-decoration-line: none;">`+y+`</a>`
                                                }).join(',')+`</span>
                                                </td>`
                html = html + `             </tr>`
                html = html + `             <tr>`
                html = html + `                 <td colspan=2>`
                html = html + `                     
                                                    <button type="button"
                                                        delete_it style='float: left; margin: 0.5rem 0rem; padding: 0.5rem;background: transparent; color: darkred; padding-left: 0px;'>
                                                        Delete
                                                    </button>
                                                    <button 
                                                        type="button"
                                                        submit_it style='float: right; margin: 0.5rem 0rem; padding: 0.5rem;'>
                                                        Save
                                                    </button>
                                                    <button 
                                                        type="button" 
                                                        close_it style='float: right; margin: 0.5rem 1rem; padding: 0.5rem; background: transparent; color: darkgray;'>
                                                        Close
                                                    </button>`
                html = html + `                 </td>`
                html = html + `             </tr>`
                html = html + `         </table>` 
                html = html + `         <table right class='`+(MyAjax.P!=true?'need_premium':'')+`'>`
                html = html + `             <tr>`
                html = html + `                 <td>`
                html = html + `                     <h2><b for></b> requests</h2>
                                                    <span no_bot style=''></span>`
                html = html + `                     <table></table>`
                html = html + `                     <div pagination current="1"></div>`                
                html = html + `                 </td>`
                html = html + `             </tr>`
                html = html + `         </table>
                                        </div>
                `
                html = html + `     </td>`
                html = html + `</tr>`
            })            
            table.innerHTML = html 
            
            let afterLoadHistory = ()=>{
                
                // sorting
                {
                    const tableBody = document.querySelector('#simplefeed_bots_protection_settings_bots_table tbody')
                    const rows = Array.from(tableBody.querySelectorAll('tr[viewing]'))                    
                    const pairs = rows.map(row => {
                        const editingRow = row.nextElementSibling;
                        return [row, editingRow];
                    })
                    const columnIndex = 4;
                    const sortedPairs = pairs.sort((a, b) => {
                        const abn = (a[0].cells[1]?.textContent.trim() || '');
                        const bbn = (b[0].cells[1]?.textContent.trim() || '');
                        if (abn === '* Please config new rule here' && bbn !== '* Please config new rule here') {
                            return -Number.MAX_SAFE_INTEGER;
                        }
                        if (bbn === '* Please config new rule here' && abn !== '* Please config new rule here') {
                            return Number.MAX_SAFE_INTEGER;
                        }
                        const av = a[0].cells[columnIndex]?.textContent.trim() || '';
                        const bv = b[0].cells[columnIndex]?.textContent.trim() || '';
                        const aValue = av!='' ? new Date(av) : new Date(0);
                        const bValue = bv!='' ? new Date(bv) : new Date(0);
                        if (aValue - bValue !== 0) {
                            return bValue - aValue
                        }
                        return abn.localeCompare(bbn)
                    })                    
                    tableBody.innerHTML = '';
                    sortedPairs.forEach(pair => {
                        tableBody.appendChild(pair[0]);
                        tableBody.appendChild(pair[1]);
                    })
                }
                // binding actions
                
                //SimpleFeedBotsProtection.bindClickDeleteAll()
                //SimpleFeedBotsProtection.bindClickHistoryAll()
                Object.keys(json).forEach(key => {
                    SimpleFeedBotsProtection.bindChangeRuleKey(key)
                    SimpleFeedBotsProtection.bindChangeRuleDenyText(key)
                    SimpleFeedBotsProtection.bindChangeRuleUserAgentContains(key)
                    SimpleFeedBotsProtection.bindChangeRuleNumberPosts(key)
                    //SimpleFeedBotsProtection.bindChangeRuleSitemapEnable(key)
                    SimpleFeedBotsProtection.bindChangeRuleTypes(key)
                    SimpleFeedBotsProtection.bindChangeRuleCategories(key)
                    SimpleFeedBotsProtection.bindChangeRuleTags(key)
                    SimpleFeedBotsProtection.bindChangeRuleEnable(key)
                    //SimpleFeedBotsProtection.bindChangeRuleAccess(key)
                    //SimpleFeedBotsProtection.bindClickRuleDelete(key)
                    SimpleFeedBotsProtection.bindClickRuleEdit(key)
                    //SimpleFeedBotsProtection.bindClickRuleHistory(key)
                })
                SimpleFeedBotsProtection.bindChangeEnable()  
                SimpleFeedBotsProtection.bindChangeEnableBackup()
                SimpleFeedBotsProtection.bindClickRuleAdd()
                SimpleFeedBotsProtection.bindClickNeedPremium()
                SimpleFeedBotsProtection.bindClickAdditionalSave()
                // paginating

                pagination.innerHTML = ``
                let pageSize = 10; let current = pagination.getAttribute('current')
                for (let i = 1; i <= Math.ceil(Object.keys(json).length / pageSize); i++) {                
                    pagination.innerHTML = pagination.innerHTML + `<a href="javascript://" class="page-number `+(current==i?'active':'')+`" data-page="`+i+`">`+i+`</a>`
                }
                const rows = document.querySelectorAll('#'+id_prefix+'_table tbody tr[viewing]');
                const paginationLinks = document.querySelectorAll("#"+id_prefix+'_pagination a.page-number');
                function showPage(page) {
                    const start = (page - 1) * pageSize;
                    const end = start + pageSize;
                    rows.forEach((row, index) => {
                        if (index >= start && index < end) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });        
                    paginationLinks.forEach(link => {
                        link.classList.remove('active');
                        if (parseInt(link.dataset.page) === page) {
                            link.classList.add('active');
                        }
                    });
                    pagination.setAttribute('current',page)
                }            
                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const page = parseInt(this.dataset.page);
                        showPage(page);
                    });
                });
                showPage(1);
            }


            SimpleFeedBotsProtection.loadHistory(undefined,undefined,true, history => {
                history.forEach(h=>{
                    let botName = h.bot
                    if (document.querySelector(`[key="`+botName+`"][path="history_last_ip"]`)!=null) {
                        document.querySelector(`[key="`+botName+`"][path="history_last_ip"]`).innerHTML = h.last_ip
                    }
                    if (document.querySelector(`[key="`+botName+`"][path="history_last_date"]`)!=null) {
                        document.querySelector(`[key="`+botName+`"][path="history_last_date"]`).innerHTML = h.last_time
                    }
                    if (document.querySelector(`[key="`+botName+`"][path="history_count"]`)!=null) {
                        document.querySelector(`[key="`+botName+`"][path="history_count"]`).innerHTML = h.count + " from " + h.tokens + " devices"
                    }
                    let json = JSON.parse(document.getElementById('simplefeed_bots_protection_settings_bots').value)
                    if (json[botName]!=undefined) {
                        let botEnable = ((json[botName] || {}).enable || false)
                        if (botEnable==false && h.has_enabled=='1') {
                            SimpleFeedBotsProtection.$(`[key="`+botName+`"][path="enable_text"]`).text('Allowed (Partial)')    
                            SimpleFeedBotsProtection.$(`[key="`+botName+`"][path="enable_label"]`).css('color','#c56f08')
                            SimpleFeedBotsProtection.$(`[key="`+botName+`"][path="enable_slider"]`).css('background-color','#f39b31') 
                        }
                    }                    
                })
                afterLoadHistory()
            })

            
        } catch (error) {
            console.error(error)   
        }
    },
    renderButtonAsJson: () => {
        const asJson = document.getElementById('asJson')
        const asJsonLabel = document.getElementById('asJsonLabel')
        if (asJson==null || asJson.checked) {
            SimpleFeedBotsProtection.renderSettingsAsTable()
            if (asJsonLabel!=null) asJsonLabel.innerHTML = "On"
        } else {
            SimpleFeedBotsProtection.renderSettingsAsJson()
            asJsonLabel.innerHTML = "Off"
        }
        if (asJson!=null) {
            SimpleFeedBotsProtection.$("#asJson").off('click').on('click', event => {
                event.preventDefault()
                //const asJson = document.getElementById('asJson')
                let id_prefix = 'simplefeed_bots_protection_settings_bots'
                document.getElementById(id_prefix+'_editor').style.display = asJson.checked ? 'block' : 'none';
                let bots_json_table = document.getElementById(id_prefix+'_table')
                bots_json_table.style.display = asJson.checked ? 'none' : 'table';
                if (asJson.checked) {
                    SimpleFeedBotsProtection.renderSettingsAsTable()
                    asJsonLabel.innerHTML = "On"
                }
                else {
                    SimpleFeedBotsProtection.renderSettingsAsJson()
                    asJsonLabel.innerHTML = "Off"
                }
            })
        }        
    },
    renderButtonResetJson: ()=>{
        const resetJson = document.getElementById('resetJson')
        resetJson.addEventListener('click', (event) => {
            event.preventDefault()
            if (confirm("Are you sure you want to replace everything with the default rules ?")) {
                let el = document.getElementById('simplefeed_bots_protection_settings_bots')
                el.value = JSON.stringify(MyAjax.default, null, 4)
                document.querySelector("form").submit.click()
            }        
        })
    },
    init: () => {
        SimpleFeedBotsProtection.renderSettingsAsJson()
        SimpleFeedBotsProtection.renderSettingsAsTable()
        SimpleFeedBotsProtection.renderButtonAsJson()
        SimpleFeedBotsProtection.renderButtonResetJson()
    }
}
jQuery(document).ready( $ => {      
    if (window.location.href.indexOf('page=simplefeed-bots-protection-settings') !== -1 || window.location.href.indexOf('page=simplefeed-bots-protection-menu') !== -1) {
        SimpleFeedBotsProtection.$ = $
        SimpleFeedBotsProtection.init()
    }    
})

document.addEventListener('DOMContentLoaded', function () {
    // editor 2
    {   debugger
        var editor2 = ace.edit("simplefeed_bots_protection_settings_additional_robotstxt_rows_editor");
        //editor2.session.setMode("ace/mode/text")
        editor2.setOptions({
            maxLines: Infinity,
            wrap: true
        });
        var element = document.getElementById('simplefeed_bots_protection_settings_additional_robotstxt_rows')
        editor2.setValue(element.value, -1);
        var adjustEditorHeight = ()=>{
            var newHeight = editor2.session.getScreenLength() * editor2.renderer.lineHeight + editor2.renderer.scrollBar.getWidth();
            editor2.container.style.height = newHeight + "px";
            editor2.resize();
        }
        editor2.session.on('change', ()=>{
            document.getElementById('simplefeed_bots_protection_settings_additional_robotstxt_rows').value = editor2.getValue();
            adjustEditorHeight()
        })
        adjustEditorHeight()
    }
    var wpFooter = document.getElementById('wpfooter');
    if (wpFooter) {
        wpFooter.style.display = 'none';
    }
});