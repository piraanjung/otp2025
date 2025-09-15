function set_org_infos(org){


    $('.org_name').html('ขามป้อม')
    $('.org_short_name').html('อบต.ข้ามป้อม')
    $('.org_type').html('องค์การบริหารส่วนตำบล')
    $('.org_phone').html('098767899')
  //  if(org[0].org_logo_path === 'khampom.png'){
        $('.orglogo').attr('src','/img/khampom.png')
        $('.orglogo_bw').attr('src','/img/khampom_bw.png')
  //  }
}

function set_twman_infos(twman){
    $('.twman_name').html(twman[0].prefix+""+twman[0].firstname+" "+twman[0].lastname)
}

function set_address_user(selected_member){
    console.log('tamboselected_membern',selected_member)
    $('#address_data').html(selected_member[0].address)
    $('.zone').html(selected_member[0].undertake_zonename)
    $('.district_and_province').html('ต.'+selected_member[0].tambon_name+' อ.'+selected_member[0].district_name+' จ.'+selected_member[0].province_name)

}


