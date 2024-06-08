import sys
import getopt
import requests
import json

urlbase = 'https://www.dorompa.tokyo/playground/shortenurl'

def usage():
    print('urlshorten.py [opts] url-to-be-shortened')
    print()
    print('  options:')
    print('    -h        Print usage.')
    print('    --help')
    print('    -t        Test whether the URL is already shortened.')
    print('    --test')
    print()

def main(argv):
    try:
        opts, args = getopt.getopt(sys.argv[1:], 'ht', ['help', 'test'])
    except getopt.GetoptError:
        usage()
        sys.exit(1)

    testonly = False

    for opt, arg in opts:
        if opt in ('-h', '--help'):
            usage()
            sys.exit(0)
        elif opt in ('-t', '--test'):
            testonly = True

    if len(args) <= 0:
        usage()
        sys.exit(1)

    url = urlbase + '?url=' + args[0]
    if testonly:
        url += '&test'
        
    response = requests.get(url)

    if response.ok:
        data = json.loads(response.content)
        if testonly:
            if data['exists']:
                print('[Registration Check] Specified URL is already shortened.')
            else:
                print('[Registration Check] Specified URL is not yet shortened.')
        else:
            if data['exists']:
                print('Specified URL is already shortened to ' + data['shortUrl'])
            else:
                print('Specified URL is shortened to ' + data['shortUrl'])
    else:
        response.raise_for_status()
    
if __name__ == "__main__":
   main(sys.argv[1:])
