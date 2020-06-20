/*
const e = React.createElement;

class Auth extends React.Component {
  
  constructor(props) {
    super(props);
    this.state = { entered: false };
  }

  render() {
    
    if (this.state.entered) {
      return 'Заходим';
  	}
    
  	return (
  	   <a href="vkstatus.tmweb.ru" onClick={() => this.setState({ entered: true })}>
   	      Войти с помощью ВКонтакте
  	   </a>
    );

   }
   
}

const domContainer = document.querySelector('#auth-form');
ReactDOM.render(e(Auth), domContainer);
*/

const e = React.createElement;

class Auth extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      urlParams: []
    };
  }

  componentDidMount() {
    fetch("http://vkstatus.tmweb.ru/model/authurl.php")
      .then(res => res.json())
      .then(
        (result) => {
          this.setState({
            isLoaded: true,
            urlParams: result
          });
        },
        // Примечание: важно обрабатывать ошибки именно здесь, а не в блоке catch(),
        // чтобы не перехватывать исключения из ошибок в самих компонентах.
        (error) => {
          this.setState({
            isLoaded: true,
            error
          });
        }
      )
  }

  render() {
    const { error, isLoaded, urlParams } = this.state;
    if (error) {
      return <div>Ошибка: {error.message}</div>;
    } else if (!isLoaded) {
      return <div>Загрузка...</div>;
    } else {
      var urlString = jQuery.param(urlParams);
      return (
        <p>
            <a href={'http://oauth.vk.com/authorize?' + urlString}>
              Войти с помощью ВКонтакте
            </a>
        </p>
      );
    }
  }
}

const domContainer = document.querySelector('#auth-form');
ReactDOM.render(e(Auth), domContainer);