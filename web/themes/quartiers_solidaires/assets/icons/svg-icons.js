const svgIcons = () => {
  const ajax = new XMLHttpRequest();
  ajax.open('GET', '../icons/icons.svg', true);
  ajax.send();
  ajax.onload = function(e) {
    var div = document.createElement('div');
    div.innerHTML = ajax.responseText;
    document.body.insertBefore(div, document.body.childNodes[0]);
  };
};

export default svgIcons;
