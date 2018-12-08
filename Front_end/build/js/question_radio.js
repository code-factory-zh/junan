$(function(){
})
function add_answer() {
    var num = $('#answers').children().length; // 已经有几个every_answers了
    $('#answers').append('<div class="every_answers"><input type="radio" name="which" value="' + (num + 1) + '"><input type="text" name="answer' + (num + 1) + '" class="form-control" placeholder="请输入答案"><span class="glyphicon glyphicon-trash" onclick="del_answer('+num+')"></span></div>')
}
// 删除第(i+1)个答案
function del_answer(i){
    console.log(i)
    // var parent = $('.every_answers').parent()
    // console.log(parent.children()[i])
    // parent.remove('[name='+(i)+']');
    $('.every_answers:nth-child('+i+')').remove();
}