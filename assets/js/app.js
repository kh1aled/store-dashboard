function confirmDelete(type){return confirm('Delete this '+type+'? This action cannot be undone.');}
document.querySelectorAll('.alert').forEach(el=>setTimeout(()=>{if(el.classList.contains('show')){try{bootstrap.Alert.getOrCreateInstance(el).close()}catch(e){}}},5000));
